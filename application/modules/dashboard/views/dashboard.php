<div class="row">
  <!--Start bus pengendapan-->
  <div class="col-md-3 col-sm-3">
    <div class="panel panel-flat border-bottom-blue-800">
      <div class="panel-body">
          <div class="media-left media-middle">
            <a href="<?php echo site_url('transaction/tap_in'); ?>" class="btn bg-blue-800 btn-flat btn-rounded btn-xs btn-icon legitRipple text-size-large"><i class="icon-bus"></i></a>
          </div>
          <div class="media-left">
            <h5 class="text-semibold no-margin text-blue-800">
              <span id="tap-in">0</span> <small class="display-block no-margin">Bus Pengendapan</small>
            </h5>
          </div>
<!--        <h5 class="text-semibold no-margin text-danger"><i class="icon-bus position-left"></i> <span id="tap-in">0</span></h5>
<span class="text-muted text-size-large">Bus Pengendapan</span>-->
      </div>
    </div>
  </div>
  <!--end bus pengendapan-->

  <!--start bus exit-->
  <div class="col-md-3 col-sm-3">
    <div class="panel  panel-flat border-bottom-green-600">
      <div class="panel-body">
        <div class="media-left media-middle">
          <a href="<?php echo site_url('transaction/check_exit'); ?>" class="btn bg-green-600 btn-flat btn-rounded btn-xs btn-icon legitRipple text-size-large"><i class="icon-exit"></i></a>
        </div>
        <div class="media-left">
          <h5 class="text-semibold no-margin text-green-600">
            <span id="exit-terminal">0</span> <small class="display-block no-margin">Exit Terminal</small>
          </h5>
        </div>
<!--        <h5 class="text-semibold no-margin text-info"><i class="icon-exit position-left text-slate"></i> <span id="exit-terminal">0</span></h5>
      <span class="text-muted text-size-large">Exit Terminal</span>-->
      </div>
    </div>
  </div>
  <!--end bus exit-->

  <!--start booking-->
  <div class="col-md-3 col-sm-3">
    <div class="panel panel-flat border-bottom-orange-400">
      <div class="panel-body">
        <div class="media-left media-middle">
          <a href="<?php echo site_url('transaction/booking'); ?>" class="btn bg-orange-400 btn-flat btn-rounded btn-xs btn-icon legitRipple text-size-large"><i class="icon-price-tags2"></i></a>
        </div>
        <div class="media-left">
          <h5 class="text-semibold no-margin text-orange-400">
            <span id="booking">0</span> <small class="display-block no-margin">Booking Tiket</small>
          </h5>
        </div>
<!--        <h5 class="text-semibold no-margin text-warning"><i class=" icon-price-tags2 position-left"></i> <span id="booking">0</span></h5>
        <span class="text-muted text-size-large">Booking</span>-->
      </div>
    </div>
  </div>
  <!--end booking-->

  <!--Start bus pen-->
  
  <!--start boarding-->
  <div class="col-md-3 col-sm-3">
    <div class="panel panel-flat border-bottom-danger">
      <div class="panel-body">
        <div class="media-left media-middle">
          <a href="<?php echo site_url('transaction/boarding'); ?>" class="btn bg-danger btn-flat btn-rounded btn-xs btn-icon legitRipple text-size-large"><i class="icon-users2"></i></a>
        </div>
        <div class="media-left">
          <h5 class="text-semibold no-margin text-danger">
            <span id="boarding">0</span> <small class="display-block no-margin">Boarding</small>
          </h5>
        </div>
<!--        <h5 class="text-semibold no-margin text-teal"><i class="icon-users2 position-left"></i> <span id="boarding">0</span></h5>
        <span class="text-muted text-size-large">Boarding</span>-->
      </div>
    </div>
  </div>
  <!--end boarding-->

</div>

<div class="row">
  <div class="col-md-8 col-sm-8">
    <div class="panel panel-flat border-bottom-blue-800">
      <div class="panel-body" style="height: 450px; width: 100%">
        <canvas id="myChart"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-4 col-sm-4">
    <div class="panel panel-white border-bottom-blue-800"  style="height:450px">
      <div class="panel-heading">
        <h6 class="panel-title">System Activity</h6>
      </div>
      <div class="panel-body">
        <ul class="media-list" id="activity">
        </ul>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo base_url('assets/js/charts/chartjs/Chart.bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/plugins/socket.io.js'); ?>"></script>
<script>
  var tapIn = $('#tap-in');
  var exitTerminal = $('#exit-terminal');
  var booking = $('#booking');
  var boarding = $('#boarding');
  var socketServer='<?php echo $this->config->item("url_socket_server").":".$this->config->item("port_socket_server") ?>';
  function getSummary() {
    $.ajax({
      'url': '<?php echo site_url('dashboard/get_summary') ?>',
      'dataType': 'json',
      'success': function(data) {
        tapIn.html(data.tap_in);
        exitTerminal.html(data.exit_terminal);
        booking.html(data.booking);
        boarding.html(data.boarding);
      },
      'error': function() {

      }
    });
  }

  var ctx = document.getElementById("myChart").getContext('2d');
  var myChart;

  function passangerChart() {
    myChart = new Chart(ctx, {
      type: 'bar',
      data: {
//        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [{
            label: 'Passenger',
//            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
              'rgba(2, 119, 189, 0.7)',
              'rgba(124, 179, 66, 0.7)',
              'rgba(255, 167, 38, 0.7)',
              'rgba(244, 67, 54, 0.7)',
              'rgba(153, 102, 255, 0.7)',
              'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
              'rgba(2, 119, 189, 1)',
              'rgba(124, 179, 66, 1)',
              'rgba(255, 167, 38, 1)',
              'rgba(244, 67, 54, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
          }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: 'Passenger Demand',
          fontSize: 16,
          padding: 15
        },
        legend: {
          display: false
        },
        scales: {
          yAxes: [{
              ticks: {
                beginAtZero: true,
                min: 0,
                callback: function(value, index, values)
                {
                  if (Math.floor(value) === value)
                  {
                    return value;
                  }
                }
              },
            }]
        }
      }
    });
  }

  function getBookingPassanger() {
    $.ajax({
      'url': '<?php echo site_url('dashboard/get_booking_passanger') ?>',
      'dataType': 'json',
      'success': function(resp) {
        var label = [];
        var data = [];

        $.each(resp, function(i, e) {
          label.push(e.shelter_name);
          data.push(e.count);
        });

        updateChart(label, data);
      },
      'error': function() {

      }
    });
  }

  function updateChart(label, data) {
    myChart.data.labels = label;
    myChart.data.datasets[0].data = data;

    myChart.update();
  }

  function initSocket() {
    var socket = io(socketServer);
//    console.log('socket');
    socket.on('activity', function(data) {
//      console.log('socket');
      updateActivity(data);
    });
  }

  function updateActivity(data) {
    var icon;
    var msg = data.action;
    var time = data.time;
    var color;

    var sto = localStorage.getItem('activity');
//    var sto = JSON.parse(localStorage.getItem('activity'));
//    console.log(sto);
    if (sto === null) {
      // console.log('ngga ada');
//      console.log('null');
      var arr = [];
      arr.push(data);
      localStorage.setItem('activity', JSON.stringify(arr));
    } else {
      // console.log(sto);
      var json = JSON.parse(localStorage.getItem('activity'));
      json.unshift(data);
      if (json[5] != null) {
//        delete json[5];
        json.pop();
      }
      localStorage.setItem('activity', JSON.stringify(json));
//      console.log('ada');
//      console.log('tidak null');
//      sto.push(data);
//      localStorage.setItem('activity', JSON.stringify(sto));
    }
//    delete sto[5];
//    sto.push(data);
//    localStorage.setItem('activity', JSON.stringify(data));


    if (data.type == 'user') {
      icon = 'icon-user';
      color = 'primary-800';
    } else if (data.type == 'bus') {
      icon = 'icon-bus';
      color = 'green-600';
    } else if (data.type == 'device') {
      icon = 'icon-archive';
      color = 'orange-400';
    } else {
      icon = ' icon-shield-check';
      color = 'danger';
    }

    var activity = '<li class="media" style="display:none">\n\
            <div class="media-left">\n\
              <a href="#" class="btn border-' + color + ' text-' + color + ' btn-flat btn-rounded btn-icon btn-xs legitRipple"><i class="' + icon + '"></i></a>\n\
            </div>\n\
            <div class="media-body">\n\
              ' + msg + '\n\
              <div class="media-annotation">' + time + '</div>\n\
            </div>\n\
          </li>';
    $('#activity').prepend(activity);
//    $('#activity li').last().remove();
    $('#activity').children().eq(5).remove();
    $('#activity li').first().show('slow');
  }

  function initActivity() {
    var activity = '';

    var sto = JSON.parse(localStorage.getItem('activity'));
    if (sto == null)
      return true;

    $.each(sto, function(i, data) {
      var icon;
      var msg = data.action;
      var time = data.time;
      var color;

      if (data.type == 'user') {
        icon = 'icon-user';
        color = 'primary-800';
      } else if (data.type == 'bus') {
        icon = 'icon-bus';
        color = 'green-600';
      } else if (data.type == 'device') {
        icon = 'icon-archive';
        color = 'orange-400';
      } else {
        icon = ' icon-shield-check';
        color = 'danger';
      }

      activity += '<li class="media">\n\
            <div class="media-left">\n\
              <a href="#" class="btn border-' + color + ' text-' + color + ' btn-flat btn-rounded btn-icon btn-xs legitRipple"><i class="' + icon + '"></i></a>\n\
            </div>\n\
            <div class="media-body">\n\
              ' + msg + '\n\
              <div class="media-annotation">' + time + '</div>\n\
            </div>\n\
          </li>';
    });

    $('#activity').html(activity);
  }

  function getRndInteger(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
  }

  $(document).ready(function() {
    // Summary
    getSummary();
    setInterval(getSummary, 60000);

    // Chart
    passangerChart();
    getBookingPassanger();
    setInterval(getBookingPassanger, 60000);

    initActivity();
    setTimeout(initSocket, 3000);

    // Dummy Activity :)
//    setInterval(function() {
//      var d = new Date().toLocaleTimeString(); // 11:18:48 AM
//      var types = ['user', 'bus', 'device'];
//      var actions = ['User Adat login via Web.', 'Bus Damri E1234AB masuk pengendapan.', 'Autogate Terminal 1A terhubung.'];
//      var idx = getRndInteger(0, types.length);
//
//      updateActivity({type: types[idx], time: d, action: actions[idx]});
//    }, 3000);
  });

</script>