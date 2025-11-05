<?php
require_once __DIR__ . '/../includes/header.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
?>
<h1>Dashboard</h1>
<div class="row">
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Gender Distribution</h5>
      <canvas id="genderChart"></canvas>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Age Groups</h5>
      <canvas id="ageChart"></canvas>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Purok Population</h5>
      <canvas id="purokChart"></canvas>
    </div>
  </div>
</div>

<script>
async function fetchJSON(q){ const r = await fetch('api.php?action='+q); return r.json(); }
let genderChart, ageChart, purokChart;
async function loadCharts(){
  const g = await fetchJSON('chart_gender');
  const a = await fetchJSON('chart_age');
  const p = await fetchJSON('chart_purok');
  const gctx = document.getElementById('genderChart');
  const actx = document.getElementById('ageChart');
  const pctx = document.getElementById('purokChart');
  if(genderChart) genderChart.destroy();
  if(ageChart) ageChart.destroy();
  if(purokChart) purokChart.destroy();
  genderChart = new Chart(gctx, { type:'pie', data:{ labels:g.labels, datasets:[{ data:g.data }] } });
  ageChart = new Chart(actx, { type:'bar', data:{ labels:a.labels, datasets:[{ label:'Residents', data:a.data }] } });
  purokChart = new Chart(pctx, { type:'line', data:{ labels:p.labels, datasets:[{ label:'Residents', data:p.data, borderColor:'#ff8c42', backgroundColor:'rgba(255,140,66,0.1)', tension:0.4 }] } });
}
loadCharts();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
