import 'package:flutter/material.dart';
import '../models/check_in.dart';
import '../services/api_service.dart';

class CheckInHistoryScreen extends StatefulWidget {
  const CheckInHistoryScreen({super.key});

  @override
  State<CheckInHistoryScreen> createState() => _CheckInHistoryScreenState();
}

class _CheckInHistoryScreenState extends State<CheckInHistoryScreen> {
  List<CheckInModel> _checkIns = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadCheckIns();
  }

  Future<void> _loadCheckIns() async {
    setState(() => _isLoading = true);
    try {
      final response = await ApiService.get('/check-ins');
      if (response['success'] == true) {
        final data = response['data'];
        final items = data is Map ? data['data'] ?? [] : data;
        _checkIns = (items as List).map((c) => CheckInModel.fromJson(c)).toList();
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
    return SafeArea(
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: const [
                Icon(Icons.history, color: Color(0xFFC41230), size: 22),
                SizedBox(width: 8),
                Text('Check-in History', style: TextStyle(
                  fontSize: 22, fontWeight: FontWeight.w700, color: Color(0xFF0f172a),
                )),
              ],
            ),
          ),
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(color: Color(0xFFC41230)))
                : _checkIns.isEmpty
                    ? Center(child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: const [
                          Icon(Icons.inbox, size: 48, color: Color(0xFF475569)),
                          SizedBox(height: 12),
                          Text('No check-ins yet', style: TextStyle(color: Color(0xFF334155))),
                        ],
                      ))
                    : RefreshIndicator(
                        onRefresh: _loadCheckIns,
                        child: ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 20),
                          itemCount: _checkIns.length,
                          itemBuilder: (_, index) {
                            final checkIn = _checkIns[index];
                            return Container(
                              margin: const EdgeInsets.only(bottom: 10),
                              padding: const EdgeInsets.all(14),
                              decoration: BoxDecoration(
                                color: const Color(0xFFffffff),
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: const Color(0xFFe2e8f0)),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    width: 44, height: 44,
                                    decoration: BoxDecoration(
                                      color: (checkIn.isVerified ? const Color(0xFF22c55e) : const Color(0xFFef4444)).withOpacity(0.15),
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: Icon(
                                      checkIn.isVerified ? Icons.check_circle : Icons.error,
                                      color: checkIn.isVerified ? const Color(0xFF22c55e) : const Color(0xFFef4444),
                                    ),
                                  ),
                                  const SizedBox(width: 14),
                                  Expanded(child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(checkIn.store['name'] ?? 'Unknown', style: const TextStyle(
                                        fontWeight: FontWeight.w600, fontSize: 16, color: Color(0xFF0f172a),
                                      )),
                                      const SizedBox(height: 4),
                                      Text(checkIn.checkedInAt, style: const TextStyle(
                                        fontSize: 14, color: Color(0xFF334155),
                                      )),
                                    ],
                                  )),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.end,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                        decoration: BoxDecoration(
                                          color: (checkIn.isVerified ? const Color(0xFF22c55e) : const Color(0xFFef4444)).withOpacity(0.15),
                                          borderRadius: BorderRadius.circular(6),
                                        ),
                                        child: Text(
                                          checkIn.isVerified ? '✓ Verified' : '✗ Failed',
                                          style: TextStyle(
                                            fontSize: 13, fontWeight: FontWeight.w600,
                                            color: checkIn.isVerified ? const Color(0xFF22c55e) : const Color(0xFFef4444),
                                          ),
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Text('${checkIn.distanceFromStore}m', style: const TextStyle(
                                        fontSize: 14, color: Color(0xFF475569),
                                      )),
                                    ],
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                      ),
          ),
        ],
      ),
    );
  }
}
