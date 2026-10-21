import 'dart:math' as math;

import 'package:flutter/material.dart';

/// Page header with an animated 3D-style graduation cap and floating books.
class HeroBanner extends StatefulWidget {
  final String title;
  final String? subtitle;
  const HeroBanner({super.key, required this.title, this.subtitle});

  @override
  State<HeroBanner> createState() => _HeroBannerState();
}

class _HeroBannerState extends State<HeroBanner>
    with SingleTickerProviderStateMixin {
  late final AnimationController _c =
      AnimationController(vsync: this, duration: const Duration(seconds: 8))
        ..repeat();

  @override
  void dispose() {
    _c.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final t = Theme.of(context).textTheme;
    return Container(
      height: 130,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(26),
        gradient: const LinearGradient(
          colors: [Color(0xFF4F46E5), Color(0xFF7C3AED), Color(0xFFC026D3)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Row(
        children: [
          Expanded(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(widget.title,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: t.headlineSmall?.copyWith(
                        color: Colors.white, fontWeight: FontWeight.w800)),
                if (widget.subtitle != null)
                  Text(widget.subtitle!,
                      style: t.bodyMedium?.copyWith(color: Colors.white70)),
              ],
            ),
          ),
          SizedBox(
            width: 110,
            child: AnimatedBuilder(
              animation: _c,
              builder: (_, __) {
                final a = _c.value * 2 * math.pi;
                return Transform(
                  alignment: Alignment.center,
                  transform: Matrix4.identity()
                    ..setEntry(3, 2, 0.002)
                    ..rotateX(-0.5)
                    ..rotateY(a),
                  child: const Icon(Icons.school_rounded,
                      size: 84, color: Colors.white),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
