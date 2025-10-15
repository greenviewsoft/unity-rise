@extends('layouts.user.app')





@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/bootstrap.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/user/styles/') }}/custom.css?var=1.2">

    <!-- <link rel="stylesheet" type="text/css" href="fonts/bootstrap-icons.css"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
        
      <style>
/* ---------- Theme tokens ---------- */
:root{
  --p1:#6a3be4; --p2:#8b5cf6; --p3:#a78bfa;
  --dark:#0f0f23; --card:#15162a; --ink:#fff; --muted:#b6c2d6;
  --success:#10b981; --warn:#f59e0b; --info:#38bdf8;
}

/* ---------- Rank Pro Card ---------- */
.rank-card{
  background: linear-gradient(180deg, rgba(26,26,46,.95), rgba(13,13,28,.98));
  border:1px solid rgba(139,92,246,.35);
  border-radius:16px;
  padding:16px;
  box-shadow:0 10px 30px rgba(106,59,228,.25);
  position:relative; overflow:hidden;
}
.rank-card::before{
  content:""; position:absolute; inset:-1px; z-index:0; pointer-events:none;
  background:
    radial-gradient(600px 180px at -10% -20%, rgba(139,92,246,.22), transparent 60%),
    radial-gradient(500px 200px at 110% 120%, rgba(99,102,241,.18), transparent 60%);
}
.rank-card-header, .rank-card-body{ position:relative; z-index:1; }
.rank-card-header{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:10px; }
.rank-title{ display:flex; gap:10px; align-items:center; }
.rank-title i{ font-size:22px; color:#a78bfa; }
.rank-title h5{ color:#fff; font-weight:800; }
.text-gradient{ background:linear-gradient(135deg,#8b5cf6,#a78bfa); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }

/* ---------- Buttons (shared) ---------- */
.rank-actions{ display:flex; gap:10px; flex-wrap:wrap; }
.pill-btn{
  display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:999px;
  font-weight:700; text-decoration:none; color:#fff; border:1px solid rgba(255,255,255,.12);
  background:linear-gradient(135deg, rgba(106,59,228,.22), rgba(139,92,246,.18));
  transition:transform .15s ease, box-shadow .25s ease, opacity .15s ease; backdrop-filter: blur(8px);
}
.pill-btn:hover{ transform:translateY(-2px); box-shadow:0 12px 30px rgba(139,92,246,.35); }
.pill-primary{ background:linear-gradient(135deg,var(--p1),var(--p2)); }
.pill-outline{ background:linear-gradient(135deg,rgba(255,255,255,.06),rgba(255,255,255,.03)); border:1px solid rgba(139,92,246,.45); }
.pill-success{ background:linear-gradient(135deg,#16a34a,#22c55e); }
.pill-disabled{ background:linear-gradient(135deg,rgba(255,255,255,.05),rgba(255,255,255,.03)); color:#cbd5e1; cursor:not-allowed; opacity:.65; }

/* ---------- Rank grid ---------- */
.rank-grid{ display:grid; grid-template-columns: 1fr; gap:16px; }
@media(min-width:768px){ .rank-grid{ grid-template-columns: 300px 1fr; } }

/* ---------- Radial progress ---------- */
.radial{
  --val: 0; /* 0..100 */
  width: 220px; height: 220px; border-radius:50%;
  background:
    conic-gradient(#8b5cf6 calc(var(--val)*1%), rgba(255,255,255,.08) 0),
    radial-gradient(circle 60px at 50% 50%, rgba(0,0,0,.35) 90%, transparent 91%),
    linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
  border:1px solid rgba(255,255,255,.08);
  display:grid; place-items:center; box-shadow: inset 0 0 30px rgba(106,59,228,.15);
}
.radial-center{ text-align:center; }
.radial-value{ font-size:34px; font-weight:800; color:#fff; letter-spacing:.5px; }
.radial-sub{ font-size:12px; color:var(--muted); margin-top:2px; }

/* ---------- Chips under radial ---------- */
.mini-stats{ display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:8px; margin-top:12px; }
.chip{
  background:linear-gradient(135deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.08); color:#fff; border-radius:12px; padding:10px;
}
.chip span{ display:block; font-size:11px; color:var(--muted); }
.chip strong{ font-size:14px; }
.chip-done{ border-color: rgba(16,185,129,.35); }
.chip-remaining{ border-color: rgba(245,158,11,.35); }
.chip-target{ border-color: rgba(139,92,246,.35); }

/* ---------- Right side (current/next + milestone) ---------- */
.rank-detail{ display:flex; flex-direction:column; gap:14px; }
.rank-badges{ display:grid; grid-template-columns: 1fr 1fr; gap:10px; }
.badge-box{
  display:flex; gap:10px; align-items:center; padding:12px; border-radius:12px;
  background: linear-gradient(135deg, rgba(255,255,255,.05), rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.08); color:#fff;
}
.badge-box i{ font-size:22px; }
.badge-box small{ display:block; font-size:11px; color:var(--muted); }
.badge-box.current i{ color:#22c55e; }
.badge-box.next i{ color:#a78bfa; }

.milestone{ margin-top:6px; }
.milestone-track{
  position:relative; height:10px; border-radius:999px; background:rgba(255,255,255,.08); overflow:hidden;
  border:1px solid rgba(255,255,255,.08);
}
.milestone-fill{
  height:100%;
  background:linear-gradient(90deg, var(--p1), var(--p2), var(--p3));
  box-shadow:0 6px 20px rgba(106,59,228,.35);
}
.milestone-dot{
  position:absolute; top:50%; transform:translate(-50%,-50%); width:14px; height:14px; border-radius:50%;
  background:rgba(255,255,255,.2); border:2px solid rgba(255,255,255,.25);
}
.milestone-dot.start{ left:0%; }
.milestone-dot.mid{ left:50%; }
.milestone-dot.end{ left:100%; transform:translate(-90%,-50%); }
.milestone-dot.active{ background:#8b5cf6; border-color:#a78bfa; box-shadow:0 0 0 6px rgba(167,139,250,.18); }
.milestone-legend{ display:flex; justify-content:space-between; font-size:11px; color:var(--muted); margin-top:6px; }

/* ---------- Requirement chips ---------- */
.req-chips{ display:flex; flex-wrap:wrap; gap:8px; margin-top:6px; }
.req-chip{
  display:flex; gap:6px; align-items:center;
  padding:8px 10px; border-radius:999px; font-size:12px; color:#fff;
  background: linear-gradient(135deg, rgba(139,92,246,.20), rgba(167,139,250,.10));
  border:1px solid rgba(167,139,250,.35);
}
.req-chip i{ color:#a78bfa; }

/* ---------- Team Statistics Card (shared styles) ---------- */
.team-card{
  background: linear-gradient(180deg, rgba(26,26,46,.95), rgba(13,13,28,.95));
  border: 1px solid rgba(139,92,246,.35);
  border-radius: 16px;
  padding: 16px;
  box-shadow: 0 10px 30px rgba(106,59,228,.25);
  position: relative; overflow: hidden;
}
.team-card::before{
  content:""; position:absolute; inset:-1px;
  background: radial-gradient(600px 200px at -10% -20%, rgba(139,92,246,.22), transparent 60%),
              radial-gradient(600px 200px at 110% 120%, rgba(99,102,241,.18), transparent 60%);
  z-index:0; pointer-events:none;
}
.team-card-header, .team-card-body{ position:relative; z-index:1; }
.team-card-header{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px; }
.team-card-header h5{ color:#fff; font-weight:800; }
.team-card-header small{ color:var(--muted); }

/* ---------- Stat tiles (shared) ---------- */
.group-title{ color:#e5e7eb; font-weight:700; margin:6px 0 10px 2px; }
.stat-grid{ display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:12px; }
.stat-grid.single{ grid-template-columns: 1fr; }
@media(min-width:768px){ .stat-grid{ grid-template-columns: repeat(4, minmax(0,1fr)); } }

.stat-tile{
  position:relative; border-radius:14px; padding:14px;
  background: linear-gradient(135deg, rgba(255,255,255,.05), rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.08); color:#fff; overflow:hidden;
  transition: transform .15s ease, box-shadow .25s ease, border-color .25s ease;
}
.stat-tile:hover{ transform: translateY(-3px); box-shadow:0 16px 40px rgba(106,59,228,.25); border-color: rgba(139,92,246,.35); }
.stat-icon{ font-size:22px; opacity:.95; margin-bottom:8px; }
.stat-meta{ line-height:1.1; }
.stat-label{ display:block; font-size:12px; color:var(--muted); }
.stat-value{ font-size:22px; font-weight:800; letter-spacing:.3px; }
.stat-suffix{ color:var(--muted); margin-left:4px; }
.stat-note{ display:block; font-size:11px; color:#cdd5e1; margin-top:4px; }

.tile-primary{ background: linear-gradient(135deg, rgba(106,59,228,.25), rgba(139,92,246,.18)); }
.tile-success{ background: linear-gradient(135deg, rgba(16,185,129,.25), rgba(5,150,105,.18)); }
.tile-warning{ background: linear-gradient(135deg, rgba(245,158,11,.25), rgba(217,119,6,.18)); }
.tile-info{ background: linear-gradient(135deg, rgba(56,189,248,.25), rgba(14,165,233,.18)); }
.tile-purple{ background: linear-gradient(135deg, rgba(124,58,237,.28), rgba(168,85,247,.20)); }
.tile-gradient{ background: linear-gradient(135deg, var(--p2), var(--p3)); }

.stat-cta{
  position:absolute; right:10px; bottom:10px;
  background: rgba(255,255,255,.12); color:#fff; border:1px solid rgba(255,255,255,.18);
  font-size:12px; padding:6px 10px; border-radius:999px; text-decoration:none; cursor:pointer;
  transition: transform .15s ease, background .2s ease;
}
.stat-cta:hover{ transform:translateY(-2px); background: rgba(255,255,255,.2); }

/* ---------- Desktop layout tweaks (left align) ---------- */
@media (min-width: 992px){
  .rank-grid{
    grid-template-columns: 300px 1fr;
    column-gap: 20px;
    justify-content: flex-start;
    align-items: start;
  }
  .rank-detail{ align-items: flex-start; max-width: none; }
  .rank-badges{
    display: flex; flex-wrap: wrap; gap: 12px;
    justify-content: flex-start; align-items: stretch; width: 100%;
  }
  .badge-box{ flex: 0 1 260px; max-width: 100%; }
  .badge-box strong{ white-space: normal; word-break: break-word; }
}
@media (min-width: 1200px){
  .team-report-area{ max-width: 1200px; margin-left: 0; margin-right: auto; }
}
@media (min-width: 1400px){
  .team-report-area{ max-width: 1320px; }
}


/* ---- Downline Table (Pro • Purple • Dark) ---- */
.downline-card{
  background: linear-gradient(180deg, rgba(26,26,46,.95), rgba(13,13,28,.98));
  border:1px solid rgba(139,92,246,.35);
  border-radius:16px;
  padding:16px;
  box-shadow:0 10px 30px rgba(106,59,228,.25);
  position:relative; overflow:hidden;
}
.downline-card::before{
  content:""; position:absolute; inset:-1px; z-index:0; pointer-events:none;
  background:
    radial-gradient(600px 180px at -10% -20%, rgba(139,92,246,.22), transparent 60%),
    radial-gradient(500px 220px at 110% 120%, rgba(99,102,241,.18), transparent 60%);
}
.downline-inner{position:relative; z-index:1;}

.downline-head{
  display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;
}
.downline-title{
  color:#fff; margin:0; font-weight:800; letter-spacing:.2px;
}
.downline-sub{ color:#b6c2d6; font-size:12px; }

.downline-tools{ display:flex; gap:10px; flex-wrap:wrap; }
.tool-input, .tool-select{
  background:linear-gradient(135deg, rgba(255,255,255,.05), rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.12);
  color:#fff; border-radius:12px; height:38px; padding:8px 12px; outline:none;
}
.tool-input::placeholder{ color:#94a3b8; }

/* Table wrapper */
.downline-table-wrap{
  border-radius:14px; overflow:hidden; border:1px solid rgba(255,255,255,.08);
  background: linear-gradient(135deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
}

/* Table base */
.table-pro{ margin:0; color:#e5e7eb; }
.table-pro thead th{
  background: linear-gradient(135deg, #6a3be4 0%, #8b5cf6 100%) !important;
  color:#fff; border:none; font-weight:700; font-size:13px; letter-spacing:.3px;
  position:sticky; top:0; z-index:2;
}
.table-pro tbody tr{
  transition: background .15s ease, transform .08s ease, box-shadow .2s ease;
}
.table-pro tbody tr:hover{
  background: rgba(139,92,246,.08);
}

/* Cells */
.table-pro td, .table-pro th{ vertical-align: middle; }
.td-end{ text-align:right; }
.money{ font-weight:800; letter-spacing:.2px; }
.money-green{ color:#10b981; }
.money-muted{ color:#cbd5e1; font-size:12px; margin-left:4px; }

/* Level badge */
.badge-level{
  background: linear-gradient(135deg, #8b5cf6, #a78bfa);
  color:#fff; border:1px solid rgba(255,255,255,.18);
  padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px;
}

/* Username cell — optional avatar dot */
.usercell{ display:flex; align-items:center; gap:10px; }
.userdot{
  width:28px; height:28px; border-radius:50%;
  background: linear-gradient(135deg, rgba(139,92,246,.35), rgba(167,139,250,.25));
  border:1px solid rgba(255,255,255,.12);
  display:grid; place-items:center; font-size:12px; color:#fff; font-weight:700;
}

/* Footer small info */
.table-footnote{ color:#94a3b8; font-size:12px; margin-top:8px; }

/* Responsive tweak */
@media (max-width: 576px){
  .tool-input{ flex:1 1 100%; width:100%; }
  .tool-select{ flex:0 0 calc(50% - 5px); }
}

/* Light blue dropdown style */
.tool-select{
  appearance: none;               /* default arrow hide (Chrome/Edge) */
  -moz-appearance: none;          /* Firefox */
  -webkit-appearance: none;       /* Safari */
  background-color: #EAF6FF !important; /* লাইট ব্লু */
  color: #0b2447;                 /* গাঢ় নীল টেক্সট */
  border: 1px solid #b9e3ff;      /* হালকা নীল বর্ডার */
  border-radius: 12px;
  height: 38px; padding: 8px 36px 8px 12px;
  outline: none;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
}

/* focus ring */
.tool-select:focus{
  border-color: #7cd1ff;
  box-shadow: 0 0 0 3px rgba(56,189,248,.25);
}

/* option রঙ (যতটা সম্ভব নেটিভ মেনুতে প্রয়োগ হবে) */
.tool-select option{
  background-color: #EAF6FF;   /* মেনু ওপেন হলে ব্যাকগ্রাউন্ড */
  color: #0b2447;
}

/* কাস্টম caret (down arrow) */
.tool-select{
  background-image:
    url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='%230b2447' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.646 6.646a.5.5 0 0 1 .708 0L8 9.293l2.646-2.647a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 16px 16px;
}

/* ডার্ক থিমে হোভার সামান্য টিন্ট */
.tool-select:hover{
  background-color: #E3F2FF;
}

/* ইনপুটটাও চাইলে মিলিয়ে দিন */
.tool-input{
  background-color: rgba(234,246,255,.65);
  border: 1px solid #b9e3ff;
  color: #0b2447;
}
.tool-input::placeholder{ color:#4b5563; }

</style>

@endsection



@section('content')
    <div class="page-content footer-clear">

        <div class="page_top_title deposit_page">

            <div class="arrow"><a href="{{ url('user/dashboard') }}"><i class="bi bi-arrow-left-circle-fill"></i></a></div>

            <h3 class="text-center">{{ __('lang.team_report') }}</h3>

            <div class="telegram_boat"></div>

        </div>



        <div class="content team-report-area">

            <!-- Rank Progress Section -->
            <!--<div class="rank-progress-section mb-4">-->
            <!--    <div class="card bg-dark text-white shadow-lg">-->
            <!--        <div class="card-body p-3">-->
            <!--            <h5 class="card-title text-white text-center mb-3">Rank Progress</h5>-->
            <!--             <div class="row g-2">-->
            <!--                 <div class="col-6 col-md-3">-->
            <!--                     <div class="text-center p-2 bg-primary bg-opacity-10 rounded">-->
            <!--                         <h6 class="text-white mb-1 small">Current Rank</h6>-->
            <!--                         <span class="badge bg-primary px-2 py-1">{{ $current_rank_requirement['name'] ?? 'Rank ' . $user_rank }}</span>-->
            <!--                     </div>-->
            <!--                 </div>-->
            <!--                 <div class="col-6 col-md-3">-->
            <!--                     <div class="text-center p-2 bg-success bg-opacity-10 rounded">-->
            <!--                         <h6 class="text-white mb-1 small">Total Business</h6>-->
            <!--                         <span class="text-success fw-bold small">{{ number_format($current_rank_requirement['business_volume'] ?? 0, 2) }} USDT</span>-->
            <!--                     </div>-->
            <!--                 </div>-->
            <!--                 <div class="col-6 col-md-3">-->
            <!--                     <div class="text-center p-2 bg-info bg-opacity-10 rounded">-->
            <!--                         <h6 class="text-white mb-1 small">Completed</h6>-->
            <!--                         <span class="text-info fw-bold small">{{ number_format($business_completed, 2) }} USDT</span>-->
            <!--                     </div>-->
            <!--                 </div>-->
            <!--                 <div class="col-6 col-md-3">-->
            <!--                     <div class="text-center p-2 bg-warning bg-opacity-10 rounded">-->
            <!--                         <h6 class="text-white mb-1 small">Remaining</h6>-->
            <!--                         <span class="text-warning fw-bold small">{{ number_format($business_remaining, 2) }} USDT</span>-->
            <!--                     </div>-->
            <!--                 </div>-->
            <!--             </div>-->
            <!--            <div class="mt-3">-->
            <!--                <div class="d-flex justify-content-between align-items-center mb-2">-->
            <!--                    <small class="text-white-50">Progress to Next Rank ({{ $next_rank_requirement['name'] ?? 'Next Level' }})</small>-->
            <!--                    <small class="text-white fw-bold">-->
            <!--                        @if($business_remaining > 0)-->
            <!--                            {{ number_format($business_remaining, 2) }} USDT remaining-->
            <!--                        @else-->
            <!--                            Rank achieved! Next: {{ number_format($next_rank_requirement['business_volume'], 2) }} USDT-->
            <!--                        @endif-->
            <!--                    </small>-->
            <!--                </div>-->
            <!--                <div class="progress" style="height: 8px;">-->
            <!--                    <div class="progress-bar bg-gradient bg-success" role="progressbar" -->
            <!--                         style="width: {{ $next_rank_requirement['business_volume'] > 0 ? min(100, ($business_completed / $next_rank_requirement['business_volume']) * 100) : 0 }}%"-->
            <!--                         aria-valuenow="{{ $business_completed }}" -->
            <!--                         aria-valuemin="0" -->
            <!--                         aria-valuemax="{{ $next_rank_requirement['business_volume'] }}">-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            
            
            
            <!-- Rank Progress (Pro • Purple • Dark) -->
@php
    $nextName = $next_rank_requirement['name'] ?? 'Next Level';
    $target    = $next_rank_requirement['business_volume'] ?? ($current_rank_requirement['business_volume'] ?? 0);
    $done      = $business_completed ?? 0;
    $remain    = max(0, ($target ?: 0) - $done);
    $pct       = $target > 0 ? min(100, round(($done / $target) * 100, 2)) : 0;
@endphp

<div class="rank-card shadow-lg mb-4">
  <div class="rank-card-header">
    <div class="rank-title">
      <i class="bi bi-trophy-fill"></i>
      <div>
        <h5 class="mb-0">Rank Progress</h5>
        <small class="text-muted">Track your journey to <span class="text-gradient">{{ $nextName }}</span></small>
      </div>
    </div>

    <div class="rank-actions">
      <a href="{{ route('user.rank.requirements') }}" class="pill-btn pill-outline">
        <i class="bi bi-list-check"></i><span>View Requirements</span>
      </a>
      @if($remain <= 0)
        <a href="{{ route('user.rank.requirements') }}#claim" class="pill-btn pill-primary">
          <i class="bi bi-stars"></i><span>Claim Rank</span>
        </a>
      @else
        <button class="pill-btn pill-disabled" disabled>
          <i class="bi bi-lock-fill"></i><span>{{ number_format($remain, 2) }} USD to unlock</span>
        </button>
      @endif
    </div>
  </div>

  <div class="rank-card-body">
    <div class="rank-grid">
      <!-- Left: Radial Progress -->
      <div class="rank-progress-wrap">
        <div class="radial" style="--val: {{ $pct }};">
          <div class="radial-center">
            <div class="radial-value">{{ rtrim(rtrim(number_format($pct,2,'.',''), '0'), '.') }}%</div>
            <div class="radial-sub">to {{ $nextName }}</div>
          </div>
        </div>

        <div class="mini-stats">
          <div class="chip chip-done">
            <span>Completed</span>
            <strong>{{ number_format($done, 2) }} USD</strong>
          </div>
          <div class="chip chip-remaining">
            <span>Remaining</span>
            <strong>{{ number_format($remain, 2) }} USD</strong>
          </div>
          <div class="chip chip-target">
            <span>Target</span>
            <strong>{{ number_format($target, 2) }} USD</strong>
          </div>
        </div>
      </div>

      <!-- Right: Linear Milestone + Current/Next -->
      <div class="rank-detail">
        <div class="rank-badges">
          <div class="badge-box current">
            <i class="bi bi-shield-fill-check"></i>
            <div>
              <small>Current Rank</small>
              <strong>{{ $current_rank_requirement['name'] ?? ('Rank ' . ($user_rank ?? '—')) }}</strong>
            </div>
          </div>
          <div class="badge-box next">
            <i class="bi bi-shield-fill-plus"></i>
            <div>
              <small>Next Rank</small>
              <strong>{{ $nextName }}</strong>
            </div>
          </div>
        </div>

        <!-- Milestone track -->
        <div class="milestone">
          <div class="milestone-track">
            <div class="milestone-fill" style="width: {{ $pct }}%"></div>
            <div class="milestone-dot start active" title="Start"></div>
            <div class="milestone-dot mid {{ $pct>=50?'active':'' }}" title="50%"></div>
            <div class="milestone-dot end {{ $pct>=100?'active':'' }}" title="Target"></div>
          </div>
          <div class="milestone-legend">
            <span>0%</span><span>50%</span><span>100%</span>
          </div>
        </div>

        <!-- Requirement chips (optional: show what matters most) -->
        <div class="req-chips">
          @if(isset($next_rank_requirement['business_volume']))
            <!--<div class="req-chip">-->
            <!--  <i class="bi bi-graph-up-arrow"></i>-->
            <!--  Team BV: {{ number_format($next_rank_requirement['business_volume'],2) }} USD-->
            <!--</div>-->
          @endif
          @if(isset($next_rank_requirement['personal_investment']))
            <div class="req-chip">
              <i class="bi bi-piggy-bank-fill"></i>
              Personal: {{ number_format($next_rank_requirement['personal_investment'],2) }} USD
            </div>
          @endif
          @if(isset($next_rank_requirement['count_level']))
            <div class="req-chip">
              <i class="bi bi-people-fill"></i>
              Directs: {{ $next_rank_requirement['count_level'] }}
            </div>
          @endif
        </div>

       
      </div>
    </div>
  </div>
</div>


          <!-- Team Statistics Section (Pro • Purple • Interactive) -->
<div class="team-statistics-section mb-4">
  <div class="team-card shadow-lg">
    <div class="team-card-header">
      <div>
        <h5 class="mb-0">Team Statistics</h5>
        <small class="text-muted">Overview of your network at a glance</small>
      </div>

      <!-- Quick Action Buttons -->
      <div class="action-row">
       
        <a href="{{ route('user.invite') }}" class="pill-btn pill-outline">
          <i class="bi bi-people-fill"></i><span>Invite Friends</span>
        </a>
        <a href="{{ url('user/investment') }}" class="pill-btn pill-success">
          <i class="bi bi-rocket-takeoff-fill"></i><span>Invest Now</span>
        </a>
      </div>
    </div>

    <div class="team-card-body">
      <!-- Direct Members -->
      <div class="stat-group">
        <h6 class="group-title">Direct Members</h6>
        <div class="stat-grid">
          <div class="stat-tile tile-primary">
            <div class="stat-icon"><i class="bi bi-person-lines-fill"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Total Direct</span>
              <span class="stat-value">{{ $direct_member_count }}</span>
            </div>
            
          </div>

          <div class="stat-tile tile-success">
            <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Active</span>
              <span class="stat-value">{{ $direct_active_members }}</span>
            </div>
           
          </div>

          <div class="stat-tile tile-warning">
            <div class="stat-icon"><i class="bi bi-pause-circle-fill"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Inactive</span>
              <span class="stat-value">{{ $direct_inactive_members }}</span>
            </div>
          
          </div>

          <div class="stat-tile tile-info">
            <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Direct Business</span>
              <span class="stat-value">{{ number_format($direct_business_total, 2) }}</span>
              <small class="stat-suffix"></small>
            </div>
           
          </div>
        </div>
      </div>

      <!-- Total Team -->
      <div class="stat-group mt-3">
        <h6 class="group-title">Total Team (All Downline Levels)</h6>
        <div class="stat-grid">
          <div class="stat-tile tile-purple">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Total Team</span>
              <span class="stat-value">{{ $total_team_members }}</span>
            </div>
        
          </div>

          <div class="stat-tile tile-success">
            <div class="stat-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Active Team</span>
              <span class="stat-value">{{ $total_active_team_members }}</span>
            </div>
            
          </div>

          <div class="stat-tile tile-warning">
            <div class="stat-icon"><i class="bi bi-moon-fill"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Inactive Team</span>
              <span class="stat-value">{{ $total_inactive_team_members }}</span>
            </div>
    
          </div>

          <div class="stat-tile tile-info">
            <div class="stat-icon"><i class="bi bi-diagram-3-fill"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Total Downline Business</span>
              <span class="stat-value">{{ number_format($total_downline_business, 2) }}</span>
              <small class="stat-suffix"></small>
            </div>
          </div>
        </div>
      </div>

      <!-- Referral Income -->
      <div class="stat-group mt-3">
        <h6 class="group-title">Referral Income</h6>
        <div class="stat-grid single">
          <div class="stat-tile tile-gradient">
            <div class="stat-icon"><i class="bi bi-gift-fill"></i></div>
            <div class="stat-meta">
              <span class="stat-label">Total Referral Income</span>
              <span class="stat-value">{{ number_format($total_referral_income, 2) }}</span>
              <small class="stat-suffix">USD</small>
              <small class="stat-note">All commission types combined</small>
            </div>
            <!--<a href="{{ route('user.team') }}#referral" class="stat-cta">Breakdown</a>-->
          </div>
        </div>
      </div>
    </div>

  </div>
</div>


            <!--<div class="team_details_area">-->
            <!--    <div class="team_deposit_content">-->
                  
            <!--                <div class="team_report_area_data">-->
            <!--                    <div class="table-responsive">-->
            <!--                        <table class="table table-bordered table-hover table-dark">-->
            <!--                            <thead class="table-dark">-->
            <!--                                <tr>-->
            <!--                                    <th scope="col" class="text-center">Level</th>-->
                                                <!--<th scope="col">Email</th>-->
            <!--                                    <th scope="col">Account</th>-->
            <!--                                    <th scope="col" class="text-end">Invest</th>-->
            <!--                                    <th scope="col" class="text-end">Earned</th>-->
                                               
            <!--                                </tr>-->
            <!--                            </thead>-->
            <!--                            <tbody>-->
            <!--                                @foreach ($refersusers as $refersuser)-->
            <!--                                    <tr>-->
            <!--                                        <td class="text-center">-->
            <!--                                            <span class="badge bg-info px-2 py-1">L{{ $refersuser->level ?? 1 }}</span>-->
            <!--                                        </td>-->
                                                    <!--<td class="text-white small">{{ $refersuser->email }}</td>-->
            <!--                                        <td class="text-white small">{{ $refersuser->username }}</td>-->
            <!--                                        <td class="text-end text-white small">{{ number_format($refersuser->total_deposit, 2) }} <span class="text-muted">$</span></td>-->
            <!--                                       <td class="text-end text-success small"> <!-- ✅ New Data -->-->
            <!--        {{ number_format($refersuser->earned_commission ?? 0, 2) }} -->
            <!--        <span class="text-muted">$</span>-->
            <!--    </td>-->
                                                   
            <!--                                    </tr>-->
            <!--                                @endforeach-->
            <!--                            </tbody>-->
            <!--                        </table>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            
            <div class="team_details_area">
  <div class="team_deposit_content">
    <div class="downline-card">
      <div class="downline-inner">
        <div class="downline-head">
          <div>
            <h5 class="downline-title">Downline Overview</h5>
            <div class="downline-sub">Your team’s investment & earnings at a glance</div>
          </div>

          <!-- Toolbar -->
          <div class="downline-tools">
            <input id="dl-search" type="text" class="tool-input" placeholder="Search account…">
            <select id="dl-level" class="tool-select">
              <option value="">All Levels</option>
              @php
                $levels = collect($refersusers)->pluck('level')->filter()->unique()->sort()->values();
              @endphp
              @foreach($levels as $lvl)
                <option value="L{{ $lvl }}">L{{ $lvl }}</option>
              @endforeach
            </select>
            <!-- চাইলে এক্সপোর্ট/কপি বাটন লাগাতে পারো: route/JS অনুযায়ী -->
            <!-- <a href="#" class="pill-btn pill-outline"><i class="bi bi-download"></i>Export</a> -->
          </div>
        </div>

        <div class="downline-table-wrap table-responsive">
          <table class="table table-hover table-pro">
            <thead>
              <tr>
                <th class="text-center" style="width:120px;">Level</th>
                <th>Account</th>
                <th class="td-end" style="width:160px;">Invest</th>
                <th class="td-end" style="width:160px;">Earned</th>
              </tr>
            </thead>
            <tbody id="dl-body">
              @forelse ($refersusers as $refersuser)
                @php
                  $lev = $refersuser->level ?? 1;
                  $name = $refersuser->username;
                  $initial = strtoupper(substr($name ?? 'U',0,1));
                  $inv = number_format($refersuser->total_deposit ?? 0, 2);
                  $earn = number_format($refersuser->earned_commission ?? 0, 2);
                @endphp
                <tr data-level="L{{ $lev }}" data-name="{{ Str::lower($name) }}">
                  <td class="text-center">
                    <span class="badge-level">L{{ $lev }}</span>
                  </td>
                  <td>
                    <div class="usercell">
                      <div class="userdot">{{ $initial }}</div>
                      <div class="text-white small">{{ $name }}</div>
                    </div>
                  </td>
                  <td class="td-end">
                    <span class="money">{{ $inv }}</span>
                    <span class="money-muted">$</span>
                  </td>
                  <td class="td-end">
                    <span class="money money-green">{{ $earn }}</span>
                    <span class="money-muted">$</span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">No downline records found</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="table-footnote">
          Tip: Use the search and level filter to quickly find a user.
        </div>
      </div>
    </div>
  </div>
</div>


        </div>



    </div>
@endsection







@section('js')



<script>
(function(){
  const body  = document.getElementById('dl-body');
  const q     = document.getElementById('dl-search');
  const lvl   = document.getElementById('dl-level');

  function applyFilter(){
    const query = (q.value || '').trim().toLowerCase();
    const level = (lvl.value || '').trim();
    const rows  = body.querySelectorAll('tr');

    rows.forEach(row=>{
      const matchesName  = !query || (row.dataset.name || '').includes(query);
      const matchesLevel = !level || row.dataset.level === level;
      row.style.display  = (matchesName && matchesLevel) ? '' : 'none';
    });
  }

  q.addEventListener('input', applyFilter);
  lvl.addEventListener('change', applyFilter);
})();
</script>


<script>
  document.addEventListener('click', function(e){
    const el = e.target.closest('.pill-btn, .stat-cta, .stat-tile');
    if(!el) return;
    const r = el.getBoundingClientRect();
    el.style.setProperty('--x', (e.clientX - r.left) + 'px');
    el.style.setProperty('--y', (e.clientY - r.top) + 'px');
  }, {passive:true});
</script>

    <script src="{{ asset('public/assets/user/scripts/') }}/bootstrap.min.js"></script>

    <script src="{{ asset('public/assets/user/scripts/') }}/custom.js"></script>

    <script>
        $('.select-dropdown__button').on('click', function() {
            $('.select-dropdown__list').toggleClass('active');
        });

        $('.select-dropdown__list-item').on('click', function() {
            var itemValue = $(this).data('value');
            console.log(itemValue);
            $('.select-dropdown__button span').text($(this).text()).parent().attr('data-value', itemValue);
            $('.select-dropdown__list').toggleClass('active');
        });
    </script>
@endsection
