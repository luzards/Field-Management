import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../models/schedule.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import '../services/notification_service.dart';
import 'weekly_schedule_screen.dart';
import 'check_in_screen.dart';
import 'check_in_history_screen.dart';
import 'news_screen.dart';
import 'profile_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  List<ScheduleModel> _todaySchedules = [];
  bool _isLoading = true;
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    NotificationService.initialize();
    _loadTodaySchedules();
  }

  Future<void> _loadTodaySchedules() async {
    setState(() => _isLoading = true);
    try {
      final today = DateFormat('yyyy-MM-dd').format(DateTime.now());
      final response = await ApiService.get('/schedules', queryParams: {'date': today});
      if (response['success'] == true) {
        _todaySchedules = (response['data'] as List)
            .map((s) => ScheduleModel.fromJson(s))
            .toList();

        // Send notifications for upcoming schedules
        for (var schedule in _todaySchedules) {
          if (schedule.status == 'pending') {
            await NotificationService.showScheduleReminder(
              schedule.store.name,
              schedule.startTime,
            );
          }
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
    final pages = [
      _buildTodayPage(),
      const WeeklyScheduleScreen(),
      const CheckInHistoryScreen(),
      const NewsScreen(),
      const ProfileScreen(),
    ];

    return Scaffold(
      body: pages[_currentIndex],
      bottomNavigationBar: Container(
        decoration: const BoxDecoration(
          color: Color(0xFFffffff),
          border: Border(top: BorderSide(color: Color(0xFFe2e8f0))),
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (i) => setState(() => _currentIndex = i),
          type: BottomNavigationBarType.fixed,
          backgroundColor: Colors.transparent,
          selectedItemColor: const Color(0xFFC41230),
          unselectedItemColor: const Color(0xFF475569),
          elevation: 0,
          selectedFontSize: 12,
          unselectedFontSize: 11,
          items: const [
            BottomNavigationBarItem(icon: Icon(Icons.today), label: 'Today'),
            BottomNavigationBarItem(icon: Icon(Icons.calendar_month), label: 'Week'),
            BottomNavigationBarItem(icon: Icon(Icons.history), label: 'History'),
            BottomNavigationBarItem(icon: Icon(Icons.newspaper), label: 'News'),
            BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profile'),
          ],
        ),
      ),
    );
  }

  Widget _buildTodayPage() {
    final auth = Provider.of<AuthService>(context);
    final user = auth.user;

    return SafeArea(
      child: RefreshIndicator(
        onRefresh: _loadTodaySchedules,
        color: const Color(0xFFC41230),
        child: ListView(
          padding: const EdgeInsets.all(20),
          children: [
            // Header
            Row(
              children: [
                Container(
                  width: 48, height: 48,
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFC41230), Color(0xFFe63946)],
                    ),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Center(child: Text(
                    user?.name.substring(0, 1).toUpperCase() ?? 'A',
                    style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w700),
                  )),
                ),
                const SizedBox(width: 14),
                Expanded(child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Hello, ${user?.name ?? 'AM'} 👋', style: const TextStyle(
                      fontSize: 20, fontWeight: FontWeight.w600, color: Color(0xFF0f172a),
                    )),
                    Text(DateFormat('EEEE, dd MMMM yyyy').format(DateTime.now()), style: const TextStyle(
                      fontSize: 15, color: Color(0xFF334155),
                    )),
                  ],
                )),
              ],
            ),
            const SizedBox(height: 28),

            // Stats row
            Row(
              children: [
                _buildStatCard('Total', '${_todaySchedules.length}', const Color(0xFF3b82f6)),
                const SizedBox(width: 12),
                _buildStatCard('Done', '${_todaySchedules.where((s) => s.status == 'completed').length}', const Color(0xFF22c55e)),
                const SizedBox(width: 12),
                _buildStatCard('Pending', '${_todaySchedules.where((s) => s.status == 'pending').length}', const Color(0xFFf59e0b)),
              ],
            ),
            const SizedBox(height: 28),

            // Today's schedule header
            const Row(
              children: [
                Icon(Icons.today, color: Color(0xFFC41230), size: 20),
                SizedBox(width: 8),
                Text("Today's Schedule", style: TextStyle(
                  fontSize: 20, fontWeight: FontWeight.w600, color: Color(0xFF0f172a),
                )),
              ],
            ),
            const SizedBox(height: 16),

            // Schedule list
            if (_isLoading)
              const Center(child: Padding(
                padding: EdgeInsets.all(40),
                child: CircularProgressIndicator(color: Color(0xFFC41230)),
              ))
            else if (_todaySchedules.isEmpty)
              _buildEmptyState()
            else
              ..._todaySchedules.map((schedule) => _buildScheduleCard(schedule)),
          ],
        ),
      ),
    );
  }

  Widget _buildStatCard(String label, String value, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: const Color(0xFFffffff),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: const Color(0xFFe2e8f0)),
        ),
        child: Column(
          children: [
            Text(value, style: TextStyle(
              fontSize: 26, fontWeight: FontWeight.w700, color: color,
            )),
            const SizedBox(height: 4),
            Text(label, style: const TextStyle(fontSize: 14, color: Color(0xFF334155))),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Container(
      padding: const EdgeInsets.all(40),
      decoration: BoxDecoration(
        color: const Color(0xFFffffff),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFe2e8f0)),
      ),
      child: const Column(
        children: [
          Icon(Icons.event_available, size: 48, color: Color(0xFF475569)),
          SizedBox(height: 12),
          Text('No schedules for today', style: TextStyle(
            color: Color(0xFF334155), fontSize: 17,
          )),
          SizedBox(height: 4),
          Text('Enjoy your free day! 🎉', style: TextStyle(
            color: Color(0xFF475569), fontSize: 15,
          )),
        ],
      ),
    );
  }

  Widget _buildScheduleCard(ScheduleModel schedule) {
    final isCompleted = schedule.status == 'completed';
    final isPending = schedule.status == 'pending';

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFFffffff),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: isCompleted ? const Color(0xFF22c55e).withOpacity(0.3) : const Color(0xFFe2e8f0),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Store name and status
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: const Color(0xFFC41230).withOpacity(0.15),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(Icons.store, color: Color(0xFFC41230), size: 20),
              ),
              const SizedBox(width: 12),
              Expanded(child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(schedule.store.name, style: const TextStyle(
                    fontWeight: FontWeight.w600, fontSize: 17, color: Color(0xFF0f172a),
                  )),
                  Text(schedule.store.address, style: const TextStyle(
                    fontSize: 14, color: Color(0xFF334155),
                  ), maxLines: 1, overflow: TextOverflow.ellipsis),
                ],
              )),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: isCompleted
                      ? const Color(0xFF22c55e).withOpacity(0.15)
                      : const Color(0xFFf59e0b).withOpacity(0.15),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  isCompleted ? '✓ Done' : 'Pending',
                  style: TextStyle(
                    fontSize: 14, fontWeight: FontWeight.w600,
                    color: isCompleted ? const Color(0xFF22c55e) : const Color(0xFFf59e0b),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),

          // Time row
          Row(
            children: [
              const Icon(Icons.access_time, size: 16, color: Color(0xFF475569)),
              const SizedBox(width: 6),
              Text('${schedule.startTime} - ${schedule.endTime}', style: const TextStyle(
                fontSize: 15, color: Color(0xFF334155),
              )),
              const Spacer(),
              if (schedule.checkIn != null)
                Text(
                  '${schedule.checkIn!.distanceFromStore}m ${schedule.checkIn!.isVerified ? "✓" : "✗"}',
                  style: TextStyle(
                    fontSize: 14, fontWeight: FontWeight.w600,
                    color: schedule.checkIn!.isVerified ? const Color(0xFF22c55e) : const Color(0xFFef4444),
                  ),
                ),
            ],
          ),

          if (schedule.notes != null && schedule.notes!.isNotEmpty) ...[
            const SizedBox(height: 8),
            Text(schedule.notes!, style: const TextStyle(
              fontSize: 14, color: Color(0xFF475569), fontStyle: FontStyle.italic,
            )),
          ],

          // Check-in button
          if (isPending) ...[
            const SizedBox(height: 14),
            SizedBox(
              width: double.infinity,
              child: Container(
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF22c55e), Color(0xFF16a34a)],
                  ),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: ElevatedButton.icon(
                  onPressed: () async {
                    final result = await Navigator.push(context, MaterialPageRoute(
                      builder: (_) => CheckInScreen(schedule: schedule),
                    ));
                    if (result == true) _loadTodaySchedules();
                  },
                  icon: const Icon(Icons.location_on, size: 18),
                  label: const Text('Check In', style: TextStyle(fontWeight: FontWeight.w600)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.transparent,
                    shadowColor: Colors.transparent,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                  ),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}
