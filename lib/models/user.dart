class User {
  final int id;
  final String? name;
  final String? email;
  final String? role;
  final String? profilePic;
  final String? dob;
  final String? education;
  final String? skills;
  final String? phone;
  final bool isVerified;
  final bool twoFactorEnabled;
  final bool profileVisible;
  final bool emailVisible;
  final bool phoneVisible;
  final String? lastLoginAt;
  final String? lastLoginIp;
  final String? gender;
  final String? bio;

  User({
    required this.id,
    this.name,
    this.email,
    this.role,
    this.profilePic,
    this.dob,
    this.education,
    this.skills,
    this.phone,
    this.isVerified = false,
    this.twoFactorEnabled = false,
    this.profileVisible = true,
    this.emailVisible = false,
    this.phoneVisible = false,
    this.lastLoginAt,
    this.lastLoginIp,
    this.gender,
    this.bio,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id:
          int.tryParse(json['id'].toString()) ??
          int.tryParse(json['user_id'].toString()) ??
          0,
      name: json['name'],
      email: json['email'],
      role: json['role'] ?? 'user',
      profilePic: json['profile_pic'],
      dob: json['dob'],
      education: json['education'],
      skills: json['skills'],
      phone: json['phone'],
      isVerified: json['is_verified'] == 1 || json['is_verified'] == true,
      twoFactorEnabled:
          json['two_factor_enabled'] == 1 || json['two_factor_enabled'] == true,
      profileVisible:
          json['profile_visible'] != 0 && json['profile_visible'] != false,
      emailVisible: json['email_visible'] == 1 || json['email_visible'] == true,
      phoneVisible: json['phone_visible'] == 1 || json['phone_visible'] == true,
      lastLoginAt: json['last_login_at'],
      lastLoginIp: json['last_login_ip'],
      gender: json['gender'],
      bio: json['bio'],
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'name': name,
    'email': email,
    'role': role,
    'profile_pic': profilePic,
    'dob': dob,
    'education': education,
    'skills': skills,
    'phone': phone,
    'is_verified': isVerified,
    'two_factor_enabled': twoFactorEnabled,
    'profile_visible': profileVisible,
    'email_visible': emailVisible,
    'phone_visible': phoneVisible,
    'last_login_at': lastLoginAt,
    'last_login_ip': lastLoginIp,
    'gender': gender,
    'bio': bio,
  };
}
