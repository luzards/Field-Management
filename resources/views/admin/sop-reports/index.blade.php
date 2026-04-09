@extends('admin.layouts.app')
@section('title', 'SOP Reports')

@section('content')
<div class="page-header">
    <div>
        <h1>SOP Reports</h1>
        <div class="breadcrumb">📊 Store SOP Scores &bull; Overall Performance</div>
    </div>
</div>

<!-- Summary Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $stores->count() }}</span>
            <span class="stat-label">Active Stores</span>
        </div>
        <div class="stat-icon blue">🏪</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $stores->sum('sop_checklists_count') }}</span>
            <span class="stat-label">Total Audits</span>
        </div>
        <div class="stat-icon green">📋</div>
    </div>
    <div class="stat-card">
        <div>
            @php
                $overallAvg = $stores->where('sop_checklists_avg_overall_value', '>', 0)->avg('sop_checklists_avg_overall_value');
            @endphp
            <span class="stat-value">{{ $overallAvg ? number_format($overallAvg, 1) : '-' }}</span>
            <span class="stat-label">Average SOP Score</span>
        </div>
        <div class="stat-icon {{ $overallAvg >= 7 ? 'green' : ($overallAvg >= 5 ? 'yellow' : 'red') }}">⭐</div>
    </div>
</div>

<!-- Store SOP Table -->
<div class="card">
    <div class="card-header">
        <h3>📊 Store SOP Scores</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Store</th>
                    <th>Address</th>
                    <th>Audits</th>
                    <th>Avg Score</th>
                    <th>Rating</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stores as $store)
                <tr>
                    <td style="font-weight:600;">{{ $store->name }}</td>
                    <td class="text-sm text-muted">{{ Str::limit($store->address, 40) }}</td>
                    <td>
                        <span class="badge badge-info">{{ $store->sop_checklists_count }}</span>
                    </td>
                    <td>
                        @if($store->sop_checklists_avg_overall_value)
                            <span style="font-weight:700;font-size:18px;color:{{ $store->sop_checklists_avg_overall_value >= 7 ? 'var(--success)' : ($store->sop_checklists_avg_overall_value >= 5 ? 'var(--warning)' : 'var(--danger)') }};">
                                {{ number_format($store->sop_checklists_avg_overall_value, 1) }}
                            </span>
                            <span class="text-muted text-sm">/ 10</span>
                        @else
                            <span class="text-muted">No data</span>
                        @endif
                    </td>
                    <td>
                        @if($store->sop_checklists_avg_overall_value)
                            @php $score = $store->sop_checklists_avg_overall_value; @endphp
                            <div style="width:80px;height:8px;background:var(--bg-input);border-radius:4px;overflow:hidden;">
                                <div style="width:{{ ($score / 10) * 100 }}%;height:100%;background:{{ $score >= 7 ? 'var(--success)' : ($score >= 5 ? 'var(--warning)' : 'var(--danger)') }};border-radius:4px;"></div>
                            </div>
                        @else
                            <span class="text-muted text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="/admin/sop-reports/{{ $store->id }}" class="btn btn-sm btn-outline">View Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-muted text-sm" style="text-align:center;padding:24px;">No stores found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
