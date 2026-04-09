import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/auth_service.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthService>(context);
    final user = auth.user;

    return SafeArea(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            const SizedBox(height: 20),

            // Avatar
            Container(
              width: 90, height: 90,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFFC41230), Color(0xFFe63946)],
                ),
                borderRadius: BorderRadius.circular(24),
              ),
              child: Center(child: Text(
                user?.name.substring(0, 1).toUpperCase() ?? 'A',
                style: const TextStyle(color: Colors.white, fontSize: 38, fontWeight: FontWeight.w700),
              )),
            ),
            const SizedBox(height: 16),
            Text(user?.name ?? 'Area Manager', style: const TextStyle(
              fontSize: 24, fontWeight: FontWeight.w700, color: Color(0xFF0f172a),
            )),
            const SizedBox(height: 4),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
              decoration: BoxDecoration(
                color: const Color(0xFFC41230).withOpacity(0.15),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Text('Area Manager', style: TextStyle(
                color: Color(0xFFC41230), fontWeight: FontWeight.w600, fontSize: 14,
              )),
            ),
            const SizedBox(height: 32),

            // Info cards
            _buildInfoTile(Icons.email_outlined, 'Email', user?.email ?? '-'),
            _buildInfoTile(Icons.phone_outlined, 'Phone', user?.phone ?? '-'),
            _buildInfoTile(Icons.location_on_outlined, 'Address', user?.address ?? '-'),

            const SizedBox(height: 32),

            // Logout button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () async {
                  final confirm = await showDialog<bool>(
                    context: context,
                    builder: (_) => AlertDialog(
                      backgroundColor: const Color(0xFFffffff),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      title: const Text('Logout', style: TextStyle(color: Color(0xFF0f172a))),
                      content: const Text('Are you sure you want to logout?', style: TextStyle(color: Color(0xFF334155))),
                      actions: [
                        TextButton(onPressed: () => Navigator.pop(context, false),
                          child: const Text('Cancel', style: TextStyle(color: Color(0xFF334155)))),
                        TextButton(onPressed: () => Navigator.pop(context, true),
                          child: const Text('Logout', style: TextStyle(color: Color(0xFFef4444)))),
                      ],
                    ),
                  );
                  if (confirm == true) await auth.logout();
                },
                icon: const Icon(Icons.logout, color: Colors.white),
                label: const Text('Logout'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFef4444),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoTile(IconData icon, String label, String value) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFFffffff),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFe2e8f0)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: const Color(0xFFC41230).withOpacity(0.15),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, size: 20, color: const Color(0xFFC41230)),
          ),
          const SizedBox(width: 14),
          Expanded(child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: const TextStyle(fontSize: 14, color: Color(0xFF475569))),
              const SizedBox(height: 2),
              Text(value, style: const TextStyle(fontSize: 16, color: Color(0xFF0f172a), fontWeight: FontWeight.w500)),
            ],
          )),
        ],
      ),
    );
  }
}
