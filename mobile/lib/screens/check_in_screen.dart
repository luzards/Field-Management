import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:image_picker/image_picker.dart';
import 'package:latlong2/latlong.dart';
import '../config/api_config.dart';
import '../models/schedule.dart';
import '../services/api_service.dart';
import '../services/location_service.dart';
import '../services/notification_service.dart';
import 'sop_checklist_screen.dart';

class CheckInScreen extends StatefulWidget {
  final ScheduleModel schedule;

  const CheckInScreen({super.key, required this.schedule});

  @override
  State<CheckInScreen> createState() => _CheckInScreenState();
}

class _CheckInScreenState extends State<CheckInScreen> {
  double? _currentLat;
  double? _currentLng;
  double? _distance;
  bool _isWithinGeofence = false;
  File? _photo;
  bool _isLoading = false;
  bool _isGettingLocation = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _getLocation();
  }

  Future<void> _getLocation() async {
    setState(() { _isGettingLocation = true; _error = null; });
    try {
      final position = await LocationService.getCurrentLocation();
      _currentLat = position.latitude;
      _currentLng = position.longitude;
      _distance = LocationService.calculateDistance(
        _currentLat!, _currentLng!,
        widget.schedule.store.latitude, widget.schedule.store.longitude,
      );
      _isWithinGeofence = _distance! <= ApiConfig.geofenceRadius;
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
    }
    if (mounted) setState(() => _isGettingLocation = false);
  }

  Future<void> _takePhoto() async {
    final picker = ImagePicker();
    final photo = await picker.pickImage(
      source: ImageSource.camera,
      imageQuality: 80,
      maxWidth: 1280,
    );
    if (photo != null && mounted) {
      setState(() => _photo = File(photo.path));
    }
  }

  Future<void> _submitCheckIn() async {
    if (_currentLat == null || _currentLng == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please wait for GPS location'), backgroundColor: Color(0xFFef4444)),
      );
      return;
    }
    if (_photo == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please take a photo first'), backgroundColor: Color(0xFFef4444)),
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      final response = await ApiService.postMultipart(
        '/check-ins',
        fields: {
          'schedule_id': widget.schedule.id.toString(),
          'latitude': _currentLat.toString(),
          'longitude': _currentLng.toString(),
        },
        filePath: _photo!.path,
        fileField: 'photo',
      );

      if (response['success'] == true) {
        final isVerified = response['data']['is_verified'] ?? false;
        final checkInId = response['data']['id'];

        await NotificationService.showCheckInSuccess(
          widget.schedule.store.name,
          isVerified,
        );

        if (mounted) {
          showDialog(
            context: context,
            barrierDismissible: false,
            builder: (_) => AlertDialog(
              backgroundColor: const Color(0xFFffffff),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              title: Icon(
                isVerified ? Icons.check_circle : Icons.warning_amber,
                color: isVerified ? const Color(0xFF22c55e) : const Color(0xFFf59e0b),
                size: 56,
              ),
              content: Column(mainAxisSize: MainAxisSize.min, children: [
                Text(
                  isVerified ? 'Check-in Verified! ✓' : 'Check-in Recorded',
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700, color: Color(0xFF0f172a)),
                ),
                const SizedBox(height: 8),
                Text(
                  response['message'] ?? '',
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Color(0xFF334155), fontSize: 16),
                ),
                const SizedBox(height: 12),
                const Text(
                  'Would you like to fill the SOP Checklist for this store?',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Color(0xFF334155), fontSize: 15),
                ),
              ]),
              actions: [
                TextButton(
                  onPressed: () { Navigator.pop(context); Navigator.pop(context, true); },
                  child: const Text('Skip', style: TextStyle(color: Color(0xFF475569), fontWeight: FontWeight.w600)),
                ),
                ElevatedButton.icon(
                  onPressed: () {
                    Navigator.pop(context);
                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(
                        builder: (_) => SopChecklistScreen(
                          checkInId: checkInId,
                          storeId: widget.schedule.store.id,
                          storeName: widget.schedule.store.name,
                        ),
                      ),
                    );
                  },
                  icon: const Icon(Icons.checklist, size: 18),
                  label: const Text('Fill SOP', style: TextStyle(fontWeight: FontWeight.w600)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFC41230),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                  ),
                ),
              ],
            ),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: const Color(0xFFef4444)),
        );
      }
    }
    if (mounted) setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    final store = widget.schedule.store;
    final storeLatLng = LatLng(store.latitude, store.longitude);

    return Scaffold(
      appBar: AppBar(title: const Text('Check In', style: TextStyle(fontWeight: FontWeight.w600))),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          // Store info card
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: const Color(0xFFffffff),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: const Color(0xFFe2e8f0)),
            ),
            child: Row(children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: const Color(0xFFC41230).withOpacity(0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.store, color: Color(0xFFC41230)),
              ),
              const SizedBox(width: 14),
              Expanded(child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(store.name, style: const TextStyle(
                    fontWeight: FontWeight.w600, fontSize: 18, color: Color(0xFF0f172a),
                  )),
                  Text(store.address, style: const TextStyle(fontSize: 15, color: Color(0xFF334155))),
                  Text('${widget.schedule.startTime} - ${widget.schedule.endTime}', style: const TextStyle(
                    fontSize: 14, color: Color(0xFF475569),
                  )),
                ],
              )),
            ]),
          ),
          const SizedBox(height: 20),

          // Map
          ClipRRect(
            borderRadius: BorderRadius.circular(14),
            child: SizedBox(
              height: 200,
              child: FlutterMap(
                options: MapOptions(
                  initialCenter: storeLatLng,
                  initialZoom: 18,
                ),
                children: [
                  TileLayer(
                    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    userAgentPackageName: 'com.example.am_tracker_app',
                  ),
                  CircleLayer(circles: [
                    CircleMarker(
                      point: storeLatLng,
                      radius: 10,
                      color: const Color(0xFFC41230).withOpacity(0.2),
                      borderColor: const Color(0xFFC41230),
                      borderStrokeWidth: 2,
                    ),
                  ]),
                  MarkerLayer(markers: [
                    Marker(point: storeLatLng, width: 40, height: 40,
                      child: const Icon(Icons.store, color: Color(0xFFC41230), size: 32)),
                    if (_currentLat != null && _currentLng != null)
                      Marker(
                        point: LatLng(_currentLat!, _currentLng!), width: 40, height: 40,
                        child: Icon(Icons.person_pin_circle,
                          color: _isWithinGeofence ? const Color(0xFF22c55e) : const Color(0xFFef4444), size: 32),
                      ),
                  ]),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),

          // GPS Status
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: const Color(0xFFffffff),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: const Color(0xFFe2e8f0)),
            ),
            child: Column(children: [
              Row(children: [
                Icon(
                  _isGettingLocation ? Icons.gps_not_fixed : (_isWithinGeofence ? Icons.gps_fixed : Icons.gps_off),
                  color: _isGettingLocation ? const Color(0xFFf59e0b) : (_isWithinGeofence ? const Color(0xFF22c55e) : const Color(0xFFef4444)),
                ),
                const SizedBox(width: 12),
                Expanded(child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _isGettingLocation ? 'Getting GPS location...'
                          : _error != null ? 'GPS Error'
                          : _isWithinGeofence ? 'Within geofence ✓'
                          : 'Outside geofence (${_distance?.toStringAsFixed(1)}m)',
                      style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16,
                        color: _isGettingLocation ? const Color(0xFFf59e0b)
                            : _error != null ? const Color(0xFFef4444)
                            : _isWithinGeofence ? const Color(0xFF22c55e)
                            : const Color(0xFFef4444)),
                    ),
                    if (_error != null)
                      Text(_error!, style: const TextStyle(fontSize: 14, color: Color(0xFFef4444))),
                    if (_distance != null)
                      Text('Distance: ${_distance!.toStringAsFixed(2)}m (max ${ApiConfig.geofenceRadius.toInt()}m)',
                        style: const TextStyle(fontSize: 14, color: Color(0xFF334155))),
                  ],
                )),
                if (!_isGettingLocation)
                  IconButton(
                    icon: const Icon(Icons.refresh, color: Color(0xFFC41230)),
                    onPressed: _getLocation,
                  ),
              ]),
            ]),
          ),
          const SizedBox(height: 20),

          // Photo section
          const Text('📷 Photo Proof', style: TextStyle(
            fontSize: 18, fontWeight: FontWeight.w600, color: Color(0xFF0f172a),
          )),
          const SizedBox(height: 12),
          GestureDetector(
            onTap: _takePhoto,
            child: Container(
              height: 200,
              width: double.infinity,
              decoration: BoxDecoration(
                color: const Color(0xFFffffff),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                  color: _photo != null ? const Color(0xFF22c55e) : const Color(0xFFe2e8f0),
                  style: _photo != null ? BorderStyle.solid : BorderStyle.none,
                ),
              ),
              child: _photo != null
                  ? ClipRRect(
                      borderRadius: BorderRadius.circular(14),
                      child: Stack(children: [
                        Image.file(_photo!, width: double.infinity, height: 200, fit: BoxFit.cover),
                        Positioned(top: 8, right: 8, child: Container(
                          padding: const EdgeInsets.all(6),
                          decoration: BoxDecoration(
                            color: Colors.black54, borderRadius: BorderRadius.circular(8),
                          ),
                          child: const Icon(Icons.camera_alt, color: Colors.white, size: 18),
                        )),
                      ]),
                    )
                  : Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: const Color(0xFFC41230).withOpacity(0.15),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: const Icon(Icons.camera_alt, size: 32, color: Color(0xFFC41230)),
                      ),
                      const SizedBox(height: 12),
                      const Text('Tap to take photo', style: TextStyle(color: Color(0xFF334155), fontSize: 16)),
                    ]),
            ),
          ),
          const SizedBox(height: 28),

          // Submit button
          SizedBox(
            width: double.infinity,
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: (_currentLat != null && _photo != null)
                      ? [const Color(0xFF22c55e), const Color(0xFF16a34a)]
                      : [const Color(0xFF475569), const Color(0xFF475569)],
                ),
                borderRadius: BorderRadius.circular(12),
              ),
              child: ElevatedButton.icon(
                onPressed: (_currentLat != null && _photo != null && !_isLoading) ? _submitCheckIn : null,
                icon: _isLoading
                    ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                    : const Icon(Icons.check_circle, color: Colors.white),
                label: Text(_isLoading ? 'Submitting...' : 'Submit Check-in',
                  style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.white)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.transparent, shadowColor: Colors.transparent,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  disabledBackgroundColor: Colors.transparent,
                ),
              ),
            ),
          ),
        ]),
      ),
    );
  }
}
