<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mobile Calls Report</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { height: 100%; overflow: hidden; }
  body { font-family: system-ui, sans-serif; font-size: 13px; background: #f4f5f7; color: #222; }

  .layout { display: flex; height: 100vh; overflow: hidden; }

  /* ── Sidebar ── */
  .sidebar {
    width: 200px; min-width: 200px;
    background: #fff; border-right: 1px solid #dde1e7;
    display: flex; flex-direction: column;
    overflow-y: auto; padding: 10px 8px; gap: 6px;
  }
  .sidebar h2 {
    font-size: 11px; font-weight: 700; color: #888;
    text-transform: uppercase; letter-spacing: .05em; margin-bottom: 2px;
  }
  .sidebar label { display: flex; flex-direction: column; gap: 2px; font-size: 11px; color: #555; }
  .sidebar input,
  .sidebar select {
    height: 26px; padding: 0 6px; border: 1px solid #ccc; border-radius: 4px;
    font-size: 12px; background: #fafafa; width: 100%;
  }
  .sidebar input:focus, .sidebar select:focus { outline: 2px solid #4f8ef7; border-color: #4f8ef7; }
  .sidebar hr { border: none; border-top: 1px solid #eee; margin: 2px 0; }
  .btn {
    height: 26px; padding: 0 10px; border: none; border-radius: 4px;
    font-size: 12px; cursor: pointer; white-space: nowrap; width: 100%;
  }
  .btn-primary { background: #4f8ef7; color: #fff; }
  .btn-primary:hover { background: #3a7ae0; }
  .btn-reset { background: #eee; color: #444; margin-top: 2px; }
  .btn-reset:hover { background: #ddd; }

  /* ── Main ── */
  .main {
    flex: 1; display: flex; flex-direction: column;
    overflow: hidden; padding: 10px; gap: 6px; min-width: 0;
  }
  h1 { font-size: 14px; font-weight: 600; color: #111; flex-shrink: 0; }

  /* ── Stats ── */
  .stats { display: flex; gap: 6px; flex-wrap: wrap; flex-shrink: 0; }
  .stat-card {
    background: #fff; border: 1px solid #dde1e7; border-radius: 5px;
    padding: 4px 12px; display: flex; flex-direction: column; align-items: center;
  }
  .stat-card .val { font-size: 16px; font-weight: 700; color: #4f8ef7; line-height: 1.2; }
  .stat-card .lbl { font-size: 10px; color: #888; }

  /* ── Pagination bar ── */
  .pagination-bar {
    display: flex; gap: 4px; align-items: center; flex-shrink: 0;
    background: #fff; border: 1px solid #dde1e7; border-radius: 5px;
    padding: 4px 8px; flex-wrap: wrap;
  }
  .pagination-bar .pg-info { font-size: 11px; color: #888; margin-right: 4px; }
  .pagination-bar a,
  .pagination-bar span {
    display: inline-block; padding: 1px 7px; border-radius: 3px;
    font-size: 11px; text-decoration: none; color: #444;
    border: 1px solid #dde1e7; background: #fafafa;
  }
  .pagination-bar a:hover { background: #e8eef9; color: #4f8ef7; }
  .pagination-bar .active span { background: #4f8ef7; color: #fff; border-color: #4f8ef7; font-weight: 600; }
  .pagination-bar .disabled span { color: #bbb; }

  /* ── Table ── */
  .table-wrap {
    flex: 1; overflow: auto; background: #fff;
    border: 1px solid #dde1e7; border-radius: 5px; min-height: 0;
  }
  table { width: 100%; border-collapse: collapse; }
  thead tr:first-child th {
    position: sticky; top: 0; z-index: 2;
    background: #e8eaed; font-size: 11px; font-weight: 700; color: #444;
    padding: 4px 8px; text-align: left; border-bottom: 1px solid #dde1e7;
    white-space: nowrap;
  }
  thead tr:last-child th {
    position: sticky; top: 22px; z-index: 2;
    background: #f0f2f5; font-size: 11px; font-weight: 600; color: #555;
    padding: 4px 8px; text-align: left; border-bottom: 2px solid #dde1e7;
    white-space: nowrap;
  }
  /* group header spanning cells */
  .th-group {
    text-align: center !important;
    border-left: 2px solid #dde1e7;
    border-right: 2px solid #dde1e7;
    font-size: 10px !important;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #777 !important;
    background: #eceef2 !important;
  }
  thead tr:last-child th.grp-pi  { border-left: 2px solid #c5cae9; }
  tbody td.grp-pi                { border-left: 2px solid #e8eaf6; }
  thead tr:last-child th.grp-pi-end { border-right: 2px solid #c5cae9; }
  tbody td.grp-pi-end            { border-right: 2px solid #e8eaf6; }

  tbody td {
    padding: 3px 8px; border-bottom: 1px solid #f0f2f5;
    font-size: 12px; white-space: nowrap;
  }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover { background: #f7f9ff; }

  .badge {
    display: inline-block; padding: 1px 7px; border-radius: 10px;
    font-size: 11px; font-weight: 500;
  }
  .badge-answered { background: #d4edda; color: #1a6130; }
  .badge-missed   { background: #fde8e8; color: #8b1a1a; }
  .badge-busy     { background: #fff3cd; color: #7d5a00; }
  .badge-default  { background: #e8eaed; color: #444; }

  .nil { color: #ccc; font-style: italic; font-size: 11px; }
  .empty { padding: 24px; text-align: center; color: #aaa; font-size: 12px; }
</style>
</head>
<body>
<div class="layout">

  {{-- Sidebar --}}
  <form method="GET" class="sidebar">
    <h2>Filters</h2>
    <label>Search
      <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="DB No / User / Campaign / Client">
    </label>
    <hr>
    <label>Campaign
      <select name="campaign_id">
        <option value="">All Campaigns</option>
        @foreach($campaigns as $c)
          <option value="{{ $c }}" @selected(($filters['campaign_id'] ?? '') === $c)>{{ $c }}</option>
        @endforeach
      </select>
    </label>
    <label>User
      <select name="user">
        <option value="">All Users</option>
        @foreach($users as $u)
          <option value="{{ $u }}" @selected(($filters['user'] ?? '') === $u)>{{ $u }}</option>
        @endforeach
      </select>
    </label>
    <label>Status
      <select name="status_name">
        <option value="">All Statuses</option>
        @foreach($statuses as $s)
          <option value="{{ $s }}" @selected(($filters['status_name'] ?? '') === $s)>{{ $s }}</option>
        @endforeach
      </select>
    </label>
    <hr>
    <label>Date From
      <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
    </label>
    <label>Date To
      <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
    </label>
    <hr>
    <button type="submit" class="btn btn-primary">Apply</button>
    <a href="{{ route('reports.mobile-calls') }}" class="btn btn-reset" style="display:block;text-align:center;text-decoration:none;line-height:26px">Reset</a>
  </form>

  {{-- Main --}}
  <div class="main">
    <h1>Mobile Calls Report</h1>

    {{-- Stats --}}
    @php
      $totalSec = (int)($stats->total_sec ?? 0);
      $avgSec   = (int)round($stats->avg_sec ?? 0);
      $fmtDur   = fn(int $s) => ($s >= 3600 ? floor($s/3600).'h ' : '') . floor(($s%3600)/60).'m ' . ($s%60).'s';
    @endphp
    <div class="stats">
      <div class="stat-card"><span class="val">{{ number_format($stats->total ?? 0) }}</span><span class="lbl">Total Calls</span></div>
      <div class="stat-card"><span class="val">{{ $fmtDur($totalSec) }}</span><span class="lbl">Total Duration</span></div>
      <div class="stat-card"><span class="val">{{ $fmtDur($avgSec) }}</span><span class="lbl">Avg Duration</span></div>
      <div class="stat-card"><span class="val">{{ number_format($calls->total()) }}</span><span class="lbl">Filtered</span></div>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
      <table>
        <thead>
          {{-- Group header row --}}
          <tr>
            <th colspan="9">Call Data</th>
            <th colspan="6" class="th-group">Project Information</th>
          </tr>
          {{-- Column header row --}}
          <tr>
            {{-- Call columns --}}
            <th>#</th>
            <th>DB No</th>
            <th>Date</th>
            <th>User</th>
            <th>Status</th>
            <th>Start</th>
            <th>End</th>
            <th>Duration</th>
            {{-- Project columns --}}
            <th class="grp-pi">Job Name</th>
            <th>Job No</th>
            <th>Client</th>
            <th>Industry</th>
            <th>Type</th>
            <th class="grp-pi-end">Centers</th>
          </tr>
        </thead>
        <tbody>
          @forelse($calls as $i => $call)
          @php
            $slug = strtolower(str_replace(' ', '-', $call->status_name));
            $badgeClass = match(true) {
              str_contains($slug, 'answer') => 'badge-answered',
              str_contains($slug, 'miss')   => 'badge-missed',
              str_contains($slug, 'busy')   => 'badge-busy',
              default                        => 'badge-default',
            };
            $dur = (int)$call->length_in_sec;
            $durStr = ($dur >= 3600 ? floor($dur/3600).'h ' : '') . floor(($dur%3600)/60).'m ' . ($dur%60).'s';
            $nil = '<span class="nil">—</span>';
          @endphp
          <tr>
            <td style="color:#aaa">{{ $calls->firstItem() + $i }}</td>
            <td><code style="font-size:11px">{{ $call->db_no }}</code></td>
            <td>{{ \Carbon\Carbon::parse($call->call_date)->format('d M Y') }}</td>
            <td>{{ $call->user }}</td>
            <td><span class="badge {{ $badgeClass }}">{{ $call->status_name }}</span></td>
            <td style="color:#666">{{ date('H:i:s', $call->start_epoch) }}</td>
            <td style="color:#666">{{ date('H:i:s', $call->end_epoch) }}</td>
            <td style="font-variant-numeric:tabular-nums">{{ $durStr }}</td>
            <td class="grp-pi">{!! $call->job_name_by_research ?? $nil !!}</td>
            <td>{!! $call->job_number ?? $nil !!}</td>
            <td>
              @if($call->client_name)
                <span title="{{ $call->client_name }}">
                  {{ \Illuminate\Support\Str::limit($call->tcn_client_name, 22) }}
                </span>
              @else
                {!! $nil !!}
              @endif
            </td>
            <td>{!! $call->tci_client_industry_name ?? $nil !!}</td>
            <td>
              @if($call->type_of_calling)
                <span class="badge badge-default">{{ $call->type_of_calling }}</span>
              @else
                {!! $nil !!}
              @endif
            </td>
            <td class="grp-pi-end">{!! $call->cati_center_names ?? $nil !!}</td>
          </tr>
          @empty
          <tr><td colspan="15" class="empty">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination BOTTOM --}}
    @if($calls->hasPages())
    <div class="pagination-bar">
      <span class="pg-info">{{ $calls->firstItem() }}–{{ $calls->lastItem() }} of {{ number_format($calls->total()) }}</span>
      {{ $calls->links() }}
    </div>
    @endif

  </div>
</div>
</body>
</html>