import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/schedule.dart';
import '../services/api_service.dart';
import 'check_in_screen.dart';

class WeeklyScheduleScreen extends StatefulWidget {
  const WeeklyScheduleScreen({super.key});

  @override
  State<WeeklyScheduleScreen> createState() => _WeeklyScheduleScreenState();
}

class _WeeklyScheduleScreenState extends State<WeeklyScheduleScreen> {
  // All schedules fetched for the week
  List<ScheduleModel> _allSchedules = [];
  // ONLY the schedules for the selected date — set explicitly, never computed lazily
  List<ScheduleModel> _displayedSchedules = [];
  bool _isLoading = true;
  late DateTime _selectedDate;
  late DateTime _weekStart;

  static DateTime _toMidnight(DateTime d) => DateTime(d.year, d.month, d.day);

  @override
  void initState() {
    super.initState();
    final today = _toMidnight(DateTime.now());
    _selectedDate = today;
    _weekStart = today.subtract(Duration(days: today.weekday - 1));
    _loadWeekSchedules();
  }

  /// Apply filter synchronously — called any time data or date changes.
  void _applyFilter() {
    final target = DateFormat('yyyy-MM-dd').format(_selectedDate);
    _displayedSchedules = _allSchedules.where((s) => s.scheduledDate == target).toList();
  }

  Future<void> _loadWeekSchedules() async {
    setState(() => _isLoading = true);
    try {
      final weekStr = DateFormat('yyyy-MM-dd').format(_weekStart);
      final response = await ApiService.get('/schedules', queryParams: {'week': weekStr});
      if (response['success'] == true) {
        final rawList = response['data'] as List;
        final parsed = <ScheduleModel>[];
        for (final item in rawList) {
          try {
            parsed.add(ScheduleModel.fromJson(item as Map<String, dynamic>));
          } catch (_) {}
        }
        // Update both lists atomically in ONE setState
        setState(() {
          _allSchedules = parsed;
          _applyFilter();
          _isLoading = false;
        });
        return;
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

  void _previousWeek() {
    final newWeekStart = _weekStart.subtract(const Duration(days: 7));
    setState(() {
      _weekStart = newWeekStart;
      _selectedDate = newWeekStart;
      _allSchedules = [];
      _displayedSchedules = [];
    });
    _loadWeekSchedules();
  }

  void _nextWeek() {
    final newWeekStart = _weekStart.add(const Duration(days: 7));
    setState(() {
      _weekStart = newWeekStart;
      _selectedDate = newWeekStart;
      _allSchedules = [];
      _displayedSchedules = [];
    });
    _loadWeekSchedules();
  }

  void _selectDate(DateTime date) {
    setState(() {
      _selectedDate = _toMidnight(date);
      _applyFilter(); // immediately compute and store filtered result
    });
  }

  @override
  Widget build(BuildContext context) {
    final weekEnd = _weekStart.add(const Duration(days: 5));
    final todayStr = DateFormat('yyyy-MM-dd').format(DateTime.now());

    return SafeArea(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // ── Header ──────────────────────────────────────────
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 0),
            child: Column(
              children: [
                const Row(
                  children: [
                    Icon(Icons.calendar_month, color: Color(0xFFC41230), size: 22),
                    SizedBox(width: 8),
                    Text('Weekly Schedule', style: TextStyle(
                      fontSize: 22, fontWeight: FontWeight.w700, color: Color(0xFF0f172a),
                    )),
                  ],
                ),
                const SizedBox(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    IconButton(
                      onPressed: _previousWeek,
                      icon: const Icon(Icons.chevron_left, size: 28, color: Color(0xFF334155)),
                    ),
                    Text(
                      '${DateFormat('dd MMM').format(_weekStart)} - ${DateFormat('dd MMM yyyy').format(weekEnd)}',
                      style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w600, color: Color(0xFF0f172a)),
                    ),
                    IconButton(
                      onPressed: _nextWeek,
                      icon: const Icon(Icons.chevron_right, size: 28, color: Color(0xFF334155)),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // ── Day Buttons ──────────────────────────────────────
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: Row(
              children: List.generate(6, (i) {
                final date = _weekStart.add(Duration(days: i));
                final dateStr = DateFormat('yyyy-MM-dd').format(date);
                final selectedStr = DateFormat('yyyy-MM-dd').format(_selectedDate);
                final isSelected = dateStr == selectedStr;
                final hasSchedules = _allSchedules.any((s) => s.scheduledDate == dateStr);

                return Expanded(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 3),
                    child: GestureDetector(
                      onTap: () => _selectDate(date),
                      child: AnimatedContainer(
                        duration: const Duration(milliseconds: 180),
                        height: 72,
                        decoration: BoxDecoration(
                          color: isSelected
                              ? const Color(0xFFC41230)
                              : (hasSchedules ? Colors.white : Colors.transparent),
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(
                            color: isSelected
                                ? const Color(0xFFC41230)
                                : (hasSchedules ? const Color(0xFFe2e8f0) : Colors.transparent),
                            width: 1.5,
                          ),
                          boxShadow: isSelected
                              ? [BoxShadow(color: const Color(0xFFC41230).withAlpha(60), blurRadius: 8, offset: const Offset(0, 3))]
                              : [],
                        ),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              DateFormat('EEE').format(date),
                              style: TextStyle(
                                fontSize: 12,
                                color: isSelected ? Colors.white70 : const Color(0xFF64748b),
                              ),
                            ),
                            const SizedBox(height: 3),
                            Text(
                              '${date.day}',
                              style: TextStyle(
                                fontSize: 19,
                                fontWeight: FontWeight.w700,
                                color: isSelected ? Colors.white : const Color(0xFF0f172a),
                              ),
                            ),
                            if (hasSchedules)
                              Container(
                                margin: const EdgeInsets.only(top: 4),
                                width: 5, height: 5,
                                decoration: BoxDecoration(
                                  color: isSelected ? Colors.white : const Color(0xFFC41230),
                                  shape: BoxShape.circle,
                                ),
                              ),
                          ],
                        ),
                      ),
                    ),
                  ),
                );
              }),
            ),
          ),

          // ── Date label ───────────────────────────────────────
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 2, 20, 4),
            child: Row(
              children: [
                Text(
                  DateFormat('EEEE, dd MMMM yyyy').format(_selectedDate),
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: Color(0xFF475569)),
                ),
                if (DateFormat('yyyy-MM-dd').format(_selectedDate) == todayStr) ...[
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: const Color(0xFFC41230).withAlpha(25),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: const Text('Today', style: TextStyle(
                      fontSize: 12, fontWeight: FontWeight.w600, color: Color(0xFFC41230),
                    )),
                  ),
                ],
              ],
            ),
          ),

          // ── Schedule list — uses _displayedSchedules (stored state, not a getter)
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(color: Color(0xFFC41230)))
                : _displayedSchedules.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.event_busy, size: 48, color: const Color(0xFF475569).withAlpha(120)),
                            const SizedBox(height: 12),
                            const Text('No schedules for this day',
                                style: TextStyle(color: Color(0xFF334155), fontSize: 15)),
                          ],
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: _loadWeekSchedules,
                        color: const Color(0xFFC41230),
                        child: ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 4),
                          itemCount: _displayedSchedules.length,
                          itemBuilder: (_, i) => _buildScheduleCard(_displayedSchedules[i]),
                        ),
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildScheduleCard(ScheduleModel schedule) {
    final isCompleted = schedule.status == 'completed';

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFe2e8f0)),
      ),
      child: Row(
        children: [
          Container(
            width: 48, height: 48,
            decoration: BoxDecoration(
              color: (isCompleted ? const Color(0xFF22c55e) : const Color(0xFFC41230)).withAlpha(38),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(
              isCompleted ? Icons.check_circle : Icons.store,
              color: isCompleted ? const Color(0xFF22c55e) : const Color(0xFFC41230),
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(schedule.store.name,
                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 16, color: Color(0xFF0f172a))),
                const SizedBox(height: 3),
                Text(
                  '${schedule.scheduledDate} • ${schedule.startTime} - ${schedule.endTime}',
                  style: const TextStyle(fontSize: 13, color: Color(0xFF334155)),
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          if (schedule.status == 'pending')
            GestureDetector(
              onTap: () async {
                final result = await Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => CheckInScreen(schedule: schedule)),
                );
                if (result == true) _loadWeekSchedules();
              },
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: const Color(0xFF22c55e).withAlpha(38),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Text('Check In',
                    style: TextStyle(color: Color(0xFF22c55e), fontSize: 14, fontWeight: FontWeight.w600)),
              ),
            )
          else
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
              decoration: BoxDecoration(
                color: (isCompleted ? const Color(0xFF22c55e) : const Color(0xFFef4444)).withAlpha(38),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                schedule.status.toUpperCase(),
                style: TextStyle(
                  fontSize: 12, fontWeight: FontWeight.w600,
                  color: isCompleted ? const Color(0xFF22c55e) : const Color(0xFFef4444),
                ),
              ),
            ),
        ],
      ),
    );
  }
}
