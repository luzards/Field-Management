class SopChecklistItem {
  final String name;
  bool checked;
  int value;

  SopChecklistItem({
    required this.name,
    this.checked = false,
    this.value = 5,
  });

  Map<String, dynamic> toJson() => {
    'name': name,
    'checked': checked,
    'value': value,
  };

  factory SopChecklistItem.fromJson(Map<String, dynamic> json) {
    return SopChecklistItem(
      name: json['name'] ?? '',
      checked: json['checked'] ?? false,
      value: json['value'] ?? 5,
    );
  }
}

class SopChecklist {
  final int? id;
  final int checkInId;
  final int storeId;
  final List<SopChecklistItem> items;
  final List<String> photos;
  final String? comments;
  final int overallValue;
  final String? createdAt;

  SopChecklist({
    this.id,
    required this.checkInId,
    required this.storeId,
    required this.items,
    this.photos = const [],
    this.comments,
    required this.overallValue,
    this.createdAt,
  });

  factory SopChecklist.fromJson(Map<String, dynamic> json) {
    return SopChecklist(
      id: json['id'],
      checkInId: json['check_in_id'] ?? 0,
      storeId: json['store_id'] ?? 0,
      items: (json['items'] as List?)
          ?.map((i) => SopChecklistItem.fromJson(i))
          .toList() ?? [],
      photos: (json['photos'] as List?)?.map((p) => p.toString()).toList() ?? [],
      comments: json['comments'],
      overallValue: json['overall_value'] ?? 5,
      createdAt: json['created_at'],
    );
  }

  /// Returns default SOP checklist items for store audits.
  static List<SopChecklistItem> defaultItems() {
    return [
      SopChecklistItem(name: 'Kebersihan Toko'),
      SopChecklistItem(name: 'Display Produk'),
      SopChecklistItem(name: 'Seragam Karyawan'),
      SopChecklistItem(name: 'Harga & Label'),
      SopChecklistItem(name: 'Promosi & Banner'),
      SopChecklistItem(name: 'Peralatan & Perlengkapan'),
      SopChecklistItem(name: 'Stok Barang'),
      SopChecklistItem(name: 'Pelayanan Pelanggan'),
    ];
  }
}
