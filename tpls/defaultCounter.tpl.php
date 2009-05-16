<style type="text/css">

.counter {
  width: 200px;
}

.counter div {
  clear: both;
}

.counter span.col-name {
  float: left;
}

.counter span.col-value {
  float: right;
}

</style>
<div class="counter">
  <div class="counter-all">
    <span class="col-name">
      All:
    </span>
    <span class="col-value">
      <tpl:all />
    </span>
  </div>
  <div class="counter-visitors">
    <span class="col-name">
      Visitors:
    </span>
    <span class="col-value">
      <tpl:visitors />
    </span>
  </div>
  <div class="counter-today">
    <span class="col-name">
      Today:
    </span>
    <span class="col-value">
      <tpl:visitors-today />
    </span>
  </div>
  <div class="counter-hour">
    <span class="col-name">
      Last hour:
    </span>
    <span class="col-value">
      <tpl:visitors-hour />
    </span>
  </div>
  <div class="counter-online">
    <span class="col-name">
      Online:
    </span>
    <span class="col-value">
      <tpl:visitors-online />
    </span>
  </div>
</div>