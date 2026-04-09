class CheckInModel {
  final int id;
  final Map<String, dynamic> store;
  final double latitude;
  final double longitude;
  final String photoUrl;
  final bool isVerified;
  final double distanceFromStore;
  final String checkedInAt;

  CheckInModel({
    required this.id,
    required this.store,
    required this.latitude,
    required this.longitude,
    required this.photoUrl,
    required this.isVerified,
    required this.distanceFromStore,
    required this.checkedInAt,
  });

  factory CheckInModel.fromJson(Map<String, dynamic> json) {
    return CheckInModel(
      id: json['id'],
      store: json['store'],
      latitude: double.parse(json['latitude'].toString()),
      longitude: double.parse(json['longitude'].toString()),
      photoUrl: json['photo_url'],
      isVerified: json['is_verified'],
      distanceFromStore: double.parse(json['distance_from_store'].toString()),
      checkedInAt: json['checked_in_at'],
    );
  }
}
