import 'package:flutter/material.dart';

/// Card with a 3D tilt that follows the finger / mouse, plus a soft glare.
class TiltCard extends StatefulWidget {
  final Widget child;
  final EdgeInsets padding;
  const TiltCard(
      {super.key, required this.child, this.padding = const EdgeInsets.all(16)});

  @override
  State<TiltCard> createState() => _TiltCardState();
}

class _TiltCardState extends State<TiltCard> {
  double _x = 0, _y = 0; // -0.5 .. 0.5

  void _update(Offset p, Size s) {
    setState(() {
      _x = (p.dx / s.width - 0.5).clamp(-0.5, 0.5);
      _y = (p.dy / s.height - 0.5).clamp(-0.5, 0.5);
    });
  }

  void _reset() => setState(() {
        _x = 0;
        _y = 0;
      });

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    return LayoutBuilder(builder: (context, c) {
      final size = Size(c.maxWidth, c.hasBoundedHeight ? c.maxHeight : 100);
      return GestureDetector(
        onPanUpdate: (d) => _update(d.localPosition, size),
        onPanEnd: (_) => _reset(),
        onPanCancel: _reset,
        child: MouseRegion(
          onHover: (e) => _update(e.localPosition, size),
          onExit: (_) => _reset(),
          child: TweenAnimationBuilder<Offset>(
            tween: Tween(end: Offset(_x, _y)),
            duration: const Duration(milliseconds: 160),
            builder: (context, o, child) => Transform(
              alignment: Alignment.center,
              transform: Matrix4.identity()
                ..setEntry(3, 2, 0.0012)
                ..rotateY(o.dx * 0.35)
                ..rotateX(-o.dy * 0.35),
              child: child,
            ),
            child: Container(
              padding: widget.padding,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(22),
                border: Border.all(
                    color: dark ? Colors.white12 : const Color(0xFFE2E8F0)),
                gradient: RadialGradient(
                  center: Alignment(_x * 2, _y * 2),
                  radius: 1.4,
                  colors: [
                    const Color(0xFF6366F1).withValues(alpha: dark ? 0.22 : 0.14),
                    dark ? const Color(0xFF11142B) : Colors.white,
                  ],
                ),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF6366F1).withValues(alpha: 0.12),
                    blurRadius: 18,
                    offset: Offset(-_x * 12, 8 - _y * 6),
                  ),
                ],
              ),
              child: widget.child,
            ),
          ),
        ),
      );
    });
  }
}
