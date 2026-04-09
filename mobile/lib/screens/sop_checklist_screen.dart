import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import '../models/sop_checklist.dart';
import '../services/api_service.dart';

class SopChecklistScreen extends StatefulWidget {
  final int checkInId;
  final int storeId;
  final String storeName;

  const SopChecklistScreen({
    super.key,
    required this.checkInId,
    required this.storeId,
    required this.storeName,
  });

  @override
  State<SopChecklistScreen> createState() => _SopChecklistScreenState();
}

class _SopChecklistScreenState extends State<SopChecklistScreen> {
  final List<SopChecklistItem> _items = SopChecklist.defaultItems();
  final _commentsController = TextEditingController();
  final List<File> _photos = [];
  int _overallValue = 5;
  bool _isSubmitting = false;
  final ImagePicker _picker = ImagePicker();

  Future<void> _pickPhotos() async {
    try {
      final images = await _picker.pickMultiImage(maxWidth: 1200, imageQuality: 80);
      if (images.isNotEmpty) {
        setState(() {
          for (var img in images) {
            if (_photos.length < 5) {
              _photos.add(File(img.path));
            }
          }
        });
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error picking images: $e'), backgroundColor: const Color(0xFFef4444)),
        );
      }
    }
  }

  Future<void> _takePhoto() async {
    try {
      final image = await _picker.pickImage(source: ImageSource.camera, maxWidth: 1200, imageQuality: 80);
      if (image != null && _photos.length < 5) {
        setState(() => _photos.add(File(image.path)));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error taking photo: $e'), backgroundColor: const Color(0xFFef4444)),
        );
      }
    }
  }

  Future<void> _submit() async {
    setState(() => _isSubmitting = true);

    try {
      final uri = Uri.parse('${ApiService.baseUrl}/sop-checklists');
      final request = await ApiService.createMultipartRequest('POST', uri);

      request.fields['check_in_id'] = widget.checkInId.toString();
      request.fields['store_id'] = widget.storeId.toString();
      request.fields['items'] = jsonEncode(_items.map((i) => i.toJson()).toList());
      request.fields['comments'] = _commentsController.text;
      request.fields['overall_value'] = _overallValue.toString();

      for (var photo in _photos) {
        request.files.add(await ApiService.createMultipartFile('photos[]', photo.path));
      }

      final response = await ApiService.sendMultipart(request);

      if (response['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('SOP Checklist submitted successfully! ✅'),
              backgroundColor: Color(0xFF22c55e),
            ),
          );
          Navigator.pop(context, true);
        }
      } else {
        throw Exception(response['message'] ?? 'Failed to submit');
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: ${e.toString().replaceFirst("Exception: ", "")}'),
            backgroundColor: const Color(0xFFef4444),
          ),
        );
      }
    }

    if (mounted) setState(() => _isSubmitting = false);
  }

  Color _scoreColor(int value) {
    if (value >= 7) return const Color(0xFF22c55e);
    if (value >= 5) return const Color(0xFFf59e0b);
    return const Color(0xFFef4444);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('SOP Checklist', style: TextStyle(fontWeight: FontWeight.w600)),
        actions: [
          if (!_isSubmitting)
            TextButton.icon(
              onPressed: _submit,
              icon: const Icon(Icons.send, size: 18),
              label: const Text('Submit'),
              style: TextButton.styleFrom(foregroundColor: const Color(0xFF22c55e)),
            ),
        ],
      ),
      body: _isSubmitting
          ? const Center(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  CircularProgressIndicator(color: Color(0xFFC41230)),
                  SizedBox(height: 16),
                  Text('Submitting SOP Checklist...', style: TextStyle(color: Color(0xFF334155))),
                ],
              ),
            )
          : ListView(
              padding: const EdgeInsets.all(20),
              children: [
                // Store info
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [const Color(0xFFC41230).withOpacity(0.15), const Color(0xFFe63946).withOpacity(0.08)],
                    ),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: const Color(0xFFC41230).withOpacity(0.3)),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.store, color: Color(0xFFC41230), size: 28),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(widget.storeName, style: const TextStyle(
                              fontSize: 18, fontWeight: FontWeight.w600, color: Color(0xFF0f172a),
                            )),
                            const Text('SOP Audit Checklist', style: TextStyle(
                              fontSize: 14, color: Color(0xFF334155),
                            )),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),

                // Overall Score
                const Text('Overall Score', style: TextStyle(
                  fontSize: 18, fontWeight: FontWeight.w600, color: Color(0xFF0f172a),
                )),
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: const Color(0xFFffffff),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: const Color(0xFFe2e8f0)),
                  ),
                  child: Column(
                    children: [
                      Text(
                        '$_overallValue',
                        style: TextStyle(
                          fontSize: 50, fontWeight: FontWeight.w800,
                          color: _scoreColor(_overallValue),
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        _overallValue >= 8 ? 'Excellent 🌟' :
                        _overallValue >= 6 ? 'Good 👍' :
                        _overallValue >= 4 ? 'Average ⚠️' : 'Needs Improvement 🔴',
                        style: TextStyle(fontSize: 16, color: _scoreColor(_overallValue)),
                      ),
                      const SizedBox(height: 16),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: List.generate(10, (i) {
                          final val = i + 1;
                          final isSelected = val == _overallValue;
                          return GestureDetector(
                            onTap: () => setState(() => _overallValue = val),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 200),
                              width: 28, height: 28,
                              decoration: BoxDecoration(
                                color: isSelected ? _scoreColor(val) : const Color(0xFFe2e8f0),
                                borderRadius: BorderRadius.circular(8),
                                border: isSelected ? null : Border.all(color: const Color(0xFF475569)),
                              ),
                              child: Center(child: Text(
                                '$val',
                                style: TextStyle(
                                  fontSize: 14, fontWeight: FontWeight.w700,
                                  color: isSelected ? Colors.white : const Color(0xFF334155),
                                ),
                              )),
                            ),
                          );
                        }),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),

                // Checklist Items
                const Text('Checklist Items', style: TextStyle(
                  fontSize: 18, fontWeight: FontWeight.w600, color: Color(0xFF0f172a),
                )),
                const SizedBox(height: 12),
                ...List.generate(_items.length, (index) => _buildChecklistItem(index)),
                const SizedBox(height: 24),

                // Photos
                Row(
                  children: [
                    const Text('Photos', style: TextStyle(
                      fontSize: 18, fontWeight: FontWeight.w600, color: Color(0xFF0f172a),
                    )),
                    const SizedBox(width: 8),
                    Text('(${_photos.length}/5)', style: const TextStyle(
                      fontSize: 15, color: Color(0xFF475569),
                    )),
                  ],
                ),
                const SizedBox(height: 12),
                _buildPhotoSection(),
                const SizedBox(height: 24),

                // Comments
                const Text('Comments', style: TextStyle(
                  fontSize: 18, fontWeight: FontWeight.w600, color: Color(0xFF0f172a),
                )),
                const SizedBox(height: 12),
                TextField(
                  controller: _commentsController,
                  maxLines: 4,
                  style: const TextStyle(color: Color(0xFF0f172a), fontSize: 16),
                  decoration: const InputDecoration(
                    hintText: 'Add your observations and comments...',
                    hintStyle: TextStyle(color: Color(0xFF475569)),
                  ),
                ),
                const SizedBox(height: 32),

                // Submit Button
                Container(
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFF22c55e), Color(0xFF16a34a)],
                    ),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: ElevatedButton.icon(
                    onPressed: _submit,
                    icon: const Icon(Icons.check_circle, size: 20),
                    label: const Text('Submit SOP Checklist', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 18)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.transparent,
                      shadowColor: Colors.transparent,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      minimumSize: const Size(double.infinity, 50),
                    ),
                  ),
                ),
                const SizedBox(height: 20),
              ],
            ),
    );
  }

  Widget _buildChecklistItem(int index) {
    final item = _items[index];
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFffffff),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: item.checked ? const Color(0xFF22c55e).withOpacity(0.3) : const Color(0xFFe2e8f0),
        ),
      ),
      child: Column(
        children: [
          Row(
            children: [
              GestureDetector(
                onTap: () => setState(() => item.checked = !item.checked),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  width: 28, height: 28,
                  decoration: BoxDecoration(
                    color: item.checked ? const Color(0xFF22c55e) : Colors.transparent,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                      color: item.checked ? const Color(0xFF22c55e) : const Color(0xFF475569),
                      width: 2,
                    ),
                  ),
                  child: item.checked
                      ? const Icon(Icons.check, color: Colors.white, size: 18)
                      : null,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(child: Text(
                item.name,
                style: TextStyle(
                  fontSize: 16, fontWeight: FontWeight.w500,
                  color: item.checked ? const Color(0xFF0f172a) : const Color(0xFF334155),
                ),
              )),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: _scoreColor(item.value).withOpacity(0.15),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  '${item.value}/10',
                  style: TextStyle(
                    fontSize: 14, fontWeight: FontWeight.w700,
                    color: _scoreColor(item.value),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          SliderTheme(
            data: SliderThemeData(
              activeTrackColor: _scoreColor(item.value),
              inactiveTrackColor: const Color(0xFFe2e8f0),
              thumbColor: _scoreColor(item.value),
              overlayColor: _scoreColor(item.value).withOpacity(0.2),
              trackHeight: 4,
            ),
            child: Slider(
              value: item.value.toDouble(),
              min: 1, max: 10,
              divisions: 9,
              onChanged: (v) => setState(() => item.value = v.round()),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPhotoSection() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFffffff),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFe2e8f0)),
      ),
      child: Column(
        children: [
          if (_photos.isNotEmpty)
            SizedBox(
              height: 100,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                itemCount: _photos.length,
                itemBuilder: (context, index) => Stack(
                  children: [
                    Container(
                      margin: const EdgeInsets.only(right: 8),
                      width: 100, height: 100,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(10),
                        image: DecorationImage(
                          image: FileImage(_photos[index]),
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                    Positioned(
                      top: 4, right: 12,
                      child: GestureDetector(
                        onTap: () => setState(() => _photos.removeAt(index)),
                        child: Container(
                          width: 22, height: 22,
                          decoration: const BoxDecoration(
                            color: Color(0xFFef4444),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.close, color: Colors.white, size: 14),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          if (_photos.isNotEmpty) const SizedBox(height: 12),
          if (_photos.length < 5)
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: _takePhoto,
                    icon: const Icon(Icons.camera_alt, size: 18),
                    label: const Text('Camera'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: const Color(0xFFC41230),
                      side: const BorderSide(color: Color(0xFFe2e8f0)),
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: _pickPhotos,
                    icon: const Icon(Icons.photo_library, size: 18),
                    label: const Text('Gallery'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: const Color(0xFFe63946),
                      side: const BorderSide(color: Color(0xFFe2e8f0)),
                      padding: const EdgeInsets.symmetric(vertical: 12),
                    ),
                  ),
                ),
              ],
            ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _commentsController.dispose();
    super.dispose();
  }
}
