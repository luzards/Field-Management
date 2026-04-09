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
  List<ScheduleModel> _schedules = [];
  bool _isLoading = true;
  DateTime _weekStart = DateTime.now().subtract(Duration(days: DateTime.now().weekday - 1));

  @override
  void initState() {
    super.initState();
    _loadWeekSchedules();
  }

  Future<void> _loadWeekSchedules() async {
    setState(() => _isLoading = true);
    try {
      final weekStr = DateFormat('yyyy-MM-dd').format(_weekStart);
      final response = await ApiService.get('/schedules', queryParams: {'week': weekStr});
      if (response['success'] == true) {
        _schedules = (response['data'] as List)
            .map((s) => ScheduleModel.fromJson(s))
            .toList();
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
    _weekStart = _weekStart.subtract(const Duration(days: 7));
    _loadWeekSchedules();
  }

  void _nextWeek() {
    _weekStart = _weekStart.add(const Duration(days: 7));
    _loadWeekSchedules();
  }

  @override
  Widget build(BuildContext context) {
    final weekEnd = _weekStart.add(const Duration(days: 6));

    return SafeArea(
      child: Column(
        children: [
          // Week navigation header
          Container(
            padding: const EdgeInsets.all(20),
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
                      icon: const Icon(Icons.chevron_left, color: Color(0xFF334155)),
                      onPressed: _previousWeek,
                    ),
                    Text(
                      '${DateFormat('dd MMM').format(_weekStart)} - ${DateFormat('dd MMM yyyy').format(weekEnd)}',
                      style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w600, color: Color(0xFF0f172a)),
                    ),
                    IconButton(
                      icon: const Icon(Icons.chevron_right, color: Color(0xFF334155)),
                      onPressed: _nextWeek,
                    ),
                  ],
                ),
              ],
            ),
          ),

          // Day tabs
          SizedBox(
            height: 70,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              itemCount: 7,
              itemBuilder: (_, index) {
                final date = _weekStart.add(Duration(days: index));
                final isToday = DateFormat('yyyy-MM-dd').format(date) == DateFormat('yyyy-MM-dd').format(DateTime.now());
                final daySchedules = _schedules.where((s) => s.scheduledDate == DateFormat('yyyy-MM-dd').format(date)).toList();
                final hasSchedules = daySchedules.isNotEmpty;

                return Container(
                  width: 52, margin: const EdgeInsets.symmetric(horizontal: 4),
                  decoration: BoxDecoration(
                    color: isToday ? const Color(0xFFC41230) : (hasSchedules ? const Color(0xFFffffff) : Colors.transparent),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: hasSchedules ? const Color(0xFFe2e8f0) : Colors.transparent),
                  ),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(DateFormat('EEE').format(date), style: TextStyle(
                        fontSize: 13, color: isToday ? Colors.white70 : const Color(0xFF334155),
                      )),
                      const SizedBox(height: 4),
                      Text('${date.day}', style: TextStyle(
                        fontSize: 18, fontWeight: FontWeight.w700,
                        color: isToday ? Colors.white : const Color(0xFF0f172a),
                      )),
                      if (hasSchedules)
                        Container(
                          margin: const EdgeInsets.only(top: 4),
                          width: 6, height: 6,
                          decoration: BoxDecoration(
                            color: isToday ? Colors.white : const Color(0xFFC41230),
                            shape: BoxShape.circle,
                          ),
                        ),
                    ],
                  ),
                );
              },
            ),
          ),

          const SizedBox(height: 12),

          // Schedule list
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(color: Color(0xFFC41230)))
                : _schedules.isEmpty
                    ? const Center(child: Text('No schedules this week', style: TextStyle(color: Color(0xFF334155))))
                    : RefreshIndicator(
                        onRefresh: _loadWeekSchedules,
                        child: ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 20),
                          itemCount: _schedules.length,
                          itemBuilder: (_, index) {
                            final schedule = _schedules[index];
                            final isCompleted = schedule.status == 'completed';

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
                                    width: 48, height: 48,
                                    decoration: BoxDecoration(
                                      color: (isCompleted ? const Color(0xFF22c55e) : const Color(0xFFC41230)).withOpacity(0.15),
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: Icon(
                                      isCompleted ? Icons.check_circle : Icons.store,
                                      color: isCompleted ? const Color(0xFF22c55e) : const Color(0xFFC41230),
                                    ),
                                  ),
                                  const SizedBox(width: 14),
                                  Expanded(child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(schedule.store.name, style: const TextStyle(
                                        fontWeight: FontWeight.w600, fontSize: 16, color: Color(0xFF0f172a),
                                      )),
                                      const SizedBox(height: 4),
                                      Text(
                                        '${schedule.scheduledDate} • ${schedule.startTime} - ${schedule.endTime}',
                                        style: const TextStyle(fontSize: 14, color: Color(0xFF334155)),
                                      ),
                                    ],
                                  )),
                                  if (schedule.status == 'pending')
                                    GestureDetector(
                                      onTap: () async {
                                        final result = await Navigator.push(context, MaterialPageRoute(
                                          builder: (_) => CheckInScreen(schedule: schedule),
                                        ));
                                        if (result == true) _loadWeekSchedules();
                                      },
                                      child: Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                        decoration: BoxDecoration(
                                          color: const Color(0xFF22c55e).withOpacity(0.15),
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: const Text('Check In', style: TextStyle(
                                          color: Color(0xFF22c55e), fontSize: 14, fontWeight: FontWeight.w600,
                                        )),
                                      ),
                                    )
                                  else
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                      decoration: BoxDecoration(
                                        color: (isCompleted ? const Color(0xFF22c55e) : const Color(0xFFef4444)).withOpacity(0.15),
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Text(schedule.status.toUpperCase(), style: TextStyle(
                                        fontSize: 13, fontWeight: FontWeight.w600,
                                        color: isCompleted ? const Color(0xFF22c55e) : const Color(0xFFef4444),
                                      )),
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
