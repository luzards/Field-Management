import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:intl/intl.dart';
import '../models/news_article.dart';
import '../services/api_service.dart';

class NewsScreen extends StatefulWidget {
  const NewsScreen({super.key});

  @override
  State<NewsScreen> createState() => _NewsScreenState();
}

class _NewsScreenState extends State<NewsScreen> {
  List<NewsArticle> _articles = [];
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadNews();
  }

  Future<void> _loadNews() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });
    try {
      final response = await ApiService.get('/news');
      if (response['success'] == true) {
        _articles = (response['data'] as List)
            .map((a) => NewsArticle.fromJson(a))
            .toList();
      }
    } catch (e) {
      _error = e.toString().replaceFirst('Exception: ', '');
    }
    if (mounted) setState(() => _isLoading = false);
  }

  Future<void> _openArticle(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Could not open the article'),
            backgroundColor: Color(0xFFef4444),
          ),
        );
      }
    }
  }

  String _formatTime(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      final now = DateTime.now();
      final diff = now.difference(date);

      if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
      if (diff.inHours < 24) return '${diff.inHours}h ago';
      if (diff.inDays < 7) return '${diff.inDays}d ago';
      return DateFormat('dd MMM yyyy').format(date);
    } catch (_) {
      return '';
    }
  }

  Color _sourceColor(String source) {
    if (source.contains('Detik')) return const Color(0xFF3b82f6);
    if (source.contains('Kompas')) return const Color(0xFFf59e0b);
    if (source.contains('CNN')) return const Color(0xFFef4444);
    return const Color(0xFFC41230);
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: RefreshIndicator(
        onRefresh: _loadNews,
        color: const Color(0xFFC41230),
        child: CustomScrollView(
          slivers: [
            // Header
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 8),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(
                      children: [
                        Icon(Icons.newspaper, color: Color(0xFFf59e0b), size: 24),
                        SizedBox(width: 10),
                        Text('Food & Trending', style: TextStyle(
                          fontSize: 24, fontWeight: FontWeight.w700, color: Color(0xFF0f172a),
                        )),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'Latest food & fastfood news from Indonesia 🇮🇩',
                      style: const TextStyle(fontSize: 15, color: Color(0xFF334155)),
                    ),
                    const SizedBox(height: 16),
                  ],
                ),
              ),
            ),

            // Content
            if (_isLoading)
              const SliverFillRemaining(
                child: Center(child: CircularProgressIndicator(color: Color(0xFFC41230))),
              )
            else if (_error != null)
              SliverFillRemaining(
                child: Center(child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.error_outline, color: Color(0xFFef4444), size: 48),
                    const SizedBox(height: 12),
                    Text(_error!, style: const TextStyle(color: Color(0xFF334155))),
                    const SizedBox(height: 16),
                    ElevatedButton.icon(
                      onPressed: _loadNews,
                      icon: const Icon(Icons.refresh, size: 18),
                      label: const Text('Retry'),
                    ),
                  ],
                )),
              )
            else if (_articles.isEmpty)
              const SliverFillRemaining(
                child: Center(child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.article_outlined, color: Color(0xFF475569), size: 48),
                    SizedBox(height: 12),
                    Text('No news available', style: TextStyle(color: Color(0xFF334155))),
                  ],
                )),
              )
            else
              SliverPadding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate(
                    (context, index) => _buildNewsCard(_articles[index]),
                    childCount: _articles.length,
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildNewsCard(NewsArticle article) {
    return GestureDetector(
      onTap: () => _openArticle(article.url),
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        decoration: BoxDecoration(
          color: const Color(0xFFffffff),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: const Color(0xFFe2e8f0)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            if (article.imageUrl.isNotEmpty)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(14)),
                child: Image.network(
                  article.imageUrl,
                  height: 180,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  errorBuilder: (_, __, ___) => Container(
                    height: 100,
                    color: const Color(0xFFe2e8f0),
                    child: const Center(
                      child: Icon(Icons.fastfood, color: Color(0xFF475569), size: 40),
                    ),
                  ),
                ),
              ),

            Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Source & time
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: _sourceColor(article.source).withOpacity(0.15),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          article.source,
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: _sourceColor(article.source),
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        _formatTime(article.publishedAt),
                        style: const TextStyle(fontSize: 13, color: Color(0xFF475569)),
                      ),
                      const Spacer(),
                      const Icon(Icons.open_in_new, size: 14, color: Color(0xFF475569)),
                    ],
                  ),
                  const SizedBox(height: 10),

                  // Title
                  Text(
                    article.title,
                    style: const TextStyle(
                      fontSize: 17,
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF0f172a),
                      height: 1.3,
                    ),
                    maxLines: 3,
                    overflow: TextOverflow.ellipsis,
                  ),

                  // Description
                  if (article.description.isNotEmpty) ...[
                    const SizedBox(height: 6),
                    Text(
                      article.description,
                      style: const TextStyle(
                        fontSize: 15,
                        color: Color(0xFF334155),
                        height: 1.4,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
