class StoreModel {
  final int id;
  final String name;
  final String address;
  final double latitude;
  final double longitude;
  final String? contactPhone;
  final String? contactName;

  StoreModel({
    required this.id,
    required this.name,
    required this.address,
    required this.latitude,
    required this.longitude,
    this.contactPhone,
    this.contactName,
  });

  factory StoreModel.fromJson(Map<String, dynamic> json) {
    return StoreModel(
      id: json['id'],
      name: json['name'],
      address: json['address'],
      latitude: double.parse(json['latitude'].toString()),
      longitude: double.parse(json['longitude'].toString()),
      contactPhone: json['contact_phone'],
      contactName: json['contact_name'],
    );
  }
}
