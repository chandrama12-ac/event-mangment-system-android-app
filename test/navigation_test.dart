import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:event_mangment/providers/auth_provider.dart';
import 'package:event_mangment/screens/login_screen.dart';
import 'package:event_mangment/screens/register_screen.dart';

class MockAuthProvider extends AuthProvider {
  @override
  bool get isLoading => false;
}

void main() {
  testWidgets('Navigation from Login to Register screen works', (
    WidgetTester tester,
  ) async {
    await tester.pumpWidget(
      MultiProvider(
        providers: [
          ChangeNotifierProvider<AuthProvider>(
            create: (_) => MockAuthProvider(),
          ),
        ],
        child: const MaterialApp(home: LoginScreen()),
      ),
    );

    // Find the register button
    final registerButton = find.byKey(const Key('create_account_button'));
    expect(registerButton, findsOneWidget);

    // Tap it
    await tester.tap(registerButton);
    await tester.pumpAndSettle();

    // Verify we are on the RegisterScreen
    expect(find.byType(RegisterScreen), findsOneWidget);
  });
}
