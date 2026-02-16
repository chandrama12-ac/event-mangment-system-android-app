class Event {
  final int id;
  final String title;
  final String description;
  final DateTime eventDate;
  final String location;
  final String? imageUrl;
  final String creatorName;
  final bool isRegistered;
  final double? latitude;
  final double? longitude;
  final String? venueAddress;
  final String category;
  final String? speakerName;
  final bool isTrending;
  final String? collegeName;
  final double rating;

  String get fullImageUrl {
    if (imageUrl == null || imageUrl!.isEmpty) {
      return "https://images.unsplash.com/photo-1540575861501-7ad0582371f1?auto=format&fit=crop&w=800&q=80";
    }
    if (imageUrl!.startsWith('http')) {
      return imageUrl!;
    }
    return "http://10.206.49.106/event_management/$imageUrl";
  }

  Event({
    required this.id,
    required this.title,
    required this.description,
    required this.eventDate,
    required this.location,
    this.imageUrl,
    required this.creatorName,
    this.isRegistered = false,
    this.latitude,
    this.longitude,
    this.venueAddress,
    this.category = 'General',
    this.speakerName,
    this.isTrending = false,
    this.collegeName,
    this.rating = 4.5,
  });

  factory Event.fromJson(Map<String, dynamic> json) {
    return Event(
      id: int.parse(json['id'].toString()),
      title: json['title'],
      description: json['description'],
      eventDate: DateTime.parse(json['event_date']),
      location: json['location'],
      imageUrl: json['image_url'],
      creatorName: json['creator_name'] ?? 'Unknown',
      isRegistered: (json['is_registered'] ?? 0).toString() == '1',
      latitude: json['latitude'] != null
          ? double.parse(json['latitude'].toString())
          : null,
      longitude: json['longitude'] != null
          ? double.parse(json['longitude'].toString())
          : null,
      venueAddress: json['venue_address'],
      category: json['category'] ?? 'General',
      speakerName: json['speaker_name'],
      isTrending: (json['is_trending'] ?? 0).toString() == '1',
      collegeName: json['college_name'],
      rating: double.parse((json['rating'] ?? 4.5).toString()),
    );
  }
}
