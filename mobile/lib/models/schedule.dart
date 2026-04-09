import 'store.dart';

class ScheduleModel {
  final int id;
  final StoreModel store;
  final String scheduledDate;
  final String startTime;
  final String endTime;
  final String? notes;
  final String status;
  final Map<String, dynamic>? createdBy;
  final CheckInInfo? checkIn;

  ScheduleModel({
    required this.id,
    required this.store,
    required this.scheduledDate,
    required this.startTime,
    required this.endTime,
    this.notes,
    required this.status,
    this.createdBy,
    this.checkIn,
  });

  factory ScheduleModel.fromJson(Map<String, dynamic> json) {
    return ScheduleModel(
      id: json['id'],
      store: StoreModel.fromJson(json['store']),
      scheduledDate: json['scheduled_date'],
      startTime: json['start_time'],
      endTime: json['end_time'],
      notes: json['notes'],
      status: json['status'],
      createdBy: json['created_by'],
      checkIn: json['check_in'] != null ? CheckInInfo.fromJson(json['check_in']) : null,
    );
  }
}

class CheckInInfo {
  final int id;
  final String checkedInAt;
  final bool isVerified;
  final double distanceFromStore;

  CheckInInfo({
    required this.id,
    required this.checkedInAt,
    required this.isVerified,
    required this.distanceFromStore,
  });

  factory CheckInInfo.fromJson(Map<String, dynamic> json) {
    return CheckInInfo(
      id: json['id'],
      checkedInAt: json['checked_in_at'],
      isVerified: json['is_verified'],
      distanceFromStore: double.parse(json['distance_from_store'].toString()),
    );
  }
}
