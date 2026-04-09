@extends('admin.layouts.app')
@section('title', 'SOP Report — ' . $store->name)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $store->name }}</h1>
        <div class="breadcrumb">
            <a href="/admin/sop-reports">SOP Reports</a> &bull; Store Detail
        </div>
    </div>
    <a href="/admin/sop-reports" class="btn btn-outline btn-sm">← Back to All Stores</a>
</div>

<!-- Store Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $totalAudits }}</span>
            <span class="stat-label">Total Audits</span>
        </div>
        <div class="stat-icon blue">📋</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value" style="color:{{ $avgScore >= 7 ? 'var(--success)' : ($avgScore >= 5 ? 'var(--warning)' : 'var(--danger)') }}">
                {{ $avgScore ? number_format($avgScore, 1) : '-' }}
            </span>
            <span class="stat-label">Average SOP Score</span>
        </div>
        <div class="stat-icon {{ $avgScore >= 7 ? 'green' : ($avgScore >= 5 ? 'yellow' : 'red') }}">⭐</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $store->address }}</span>
            <span class="stat-label">Address</span>
        </div>
        <div class="stat-icon blue">📍</div>
    </div>
</div>

<!-- Score Distribution -->
@if($totalAudits > 0)
<div class="card">
    <div class="card-header">
        <h3>📊 Score Distribution</h3>
    </div>
    <div style="display:flex;align-items:flex-end;gap:6px;height:100px;padding:10px 0;">
        @for($i = 1; $i <= 10; $i++)
            @php
                $count = $distribution[$i] ?? 0;
                $maxCount = max(array_values($distribution ?: [1]));
                $height = $maxCount > 0 ? ($count / $maxCount) * 80 : 0;
                $color = $i >= 7 ? 'var(--success)' : ($i >= 5 ? 'var(--warning)' : 'var(--danger)');
            @endphp
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                <span class="text-sm" style="color:var(--text-muted);font-size:11px;">{{ $count }}</span>
                <div style="width:100%;height:{{ max($height, 4) }}px;background:{{ $color }};border-radius:4px 4px 0 0;transition:height 0.3s;"></div>
                <span class="text-sm" style="font-weight:600;font-size:12px;">{{ $i }}</span>
            </div>
        @endfor
    </div>
</div>
@endif

<!-- Audit History -->
<div class="card">
    <div class="card-header">
        <h3>📝 Audit History</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>AM</th>
                    <th>Score</th>
                    <th>Items</th>
                    <th>Comments</th>
                    <th>Photos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checklists as $cl)
                <tr>
                    <td class="text-sm">{{ $cl->created_at->format('d M Y H:i') }}</td>
                    <td style="font-weight:500;">{{ $cl->user->name }}</td>
                    <td>
                        <span style="font-weight:700;font-size:18px;color:{{ $cl->overall_value >= 7 ? 'var(--success)' : ($cl->overall_value >= 5 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $cl->overall_value }}
                        </span>
                        <span class="text-muted text-sm">/ 10</span>
                    </td>
                    <td>
                        @if(is_array($cl->items))
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                @foreach($cl->items as $item)
                                    <span class="badge {{ ($item['checked'] ?? false) ? 'badge-success' : 'badge-danger' }}" style="font-size:11px;">
                                        {{ $item['checked'] ?? false ? '✓' : '✗' }} {{ $item['name'] ?? '' }}
                                        @if(isset($item['value']))
                                            ({{ $item['value'] }})
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="text-sm" style="max-width:200px;">
                        {{ $cl->comments ? Str::limit($cl->comments, 60) : '—' }}
                    </td>
                    <td>
                        @if(is_array($cl->photos) && count($cl->photos) > 0)
                            <div style="display:flex;gap:4px;">
                                @foreach($cl->photos as $photo)
                                    <a href="/storage/{{ $photo }}" target="_blank">
                                        <img src="/storage/{{ $photo }}" alt="SOP Photo" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted text-sm">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-muted text-sm" style="text-align:center;padding:24px;">No audits found for this store</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($checklists->hasPages())
    <div class="pagination">
        {{ $checklists->links() }}
    </div>
    @endif
</div>
@endsection
