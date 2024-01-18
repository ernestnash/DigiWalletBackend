// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}


// Accumulate transactions by date
// var accumulatedData = {};

// transactionsData.forEach(function (amount, index) {
//   var label = transactionLabels[index];
//   if (!accumulatedData[label]) {
//     accumulatedData[label] = 0;
//   }
//   accumulatedData[label] += amount;
// });

// // Convert accumulated data object to arrays for chart labels and data
// var accumulatedLabels = Object.keys(accumulatedData);
// var accumulatedValues = Object.values(accumulatedData);
// // Get the current date
// var currentDate = new Date();

// Extract data from receivedData object and convert it to arrays of objects
// var receivedDataPoints = Object.entries(receivedData).map(function ([date, amount]) {
//   return {
//     x: moment(date).format('DD/MM/YY'),
//     y: parseInt(amount), 
//   };
// });
// var sentDataPoints = Object.entries(sentData).map(function ([date, amount]) {
//   return {
//     x: moment(date).format('DD/MM/YY'),
//     y: parseInt(amount), 
//   };
// });
// var depositDataPoints = Object.entries(depositData).map(function ([date, amount]) {
//   return {
//     x: moment(date).format('DD/MM/YY'),
//     y: parseInt(amount), 
//   };
// });
// var withdrawalDataPoints = Object.entries(withdrawalData).map(function ([date, amount]) {
//   return {
//     x: moment(date).format('DD/MM/YY'),
//     y: parseInt(amount), 
//   };
// });



var accumulatedData = {};

transactionLabels.forEach(function (label, index) {
  if (!accumulatedData[label]) {
    accumulatedData[label] = 0;
  }
  accumulatedData[label] += transactionsData[index];
});

// // Convert accumulated data object to arrays for chart labels and data
var accumulatedLabels = Object.keys(accumulatedData);
// var accumulatedValues = Object.values(accumulatedData);

// Function to group transactions by date and sum the amounts
function groupAndSumTransactions(data) {
  var groupedData = {};
  data.forEach(function ([date, amount]) {
    var formattedDate = moment(date).format('DD/MM/YY');
    if (!groupedData[formattedDate]) {
      groupedData[formattedDate] = 0;
    }
    groupedData[formattedDate] += parseInt(amount);
  });

  // Convert the grouped data object to an array of objects
  return Object.entries(groupedData).map(function ([date, totalAmount]) {
    return {
      x: date,
      y: totalAmount,
    };
  });
}

// Use the function to group and sum transactions for each type
var accumulatedValues = groupAndSumTransactions(Object.entries(accumulatedData));
var receivedDataPoints = groupAndSumTransactions(Object.entries(receivedData));
var sentDataPoints = groupAndSumTransactions(Object.entries(sentData));
var depositDataPoints = groupAndSumTransactions(Object.entries(depositData));
var withdrawalDataPoints = groupAndSumTransactions(Object.entries(withdrawalData));


console.log('Labels:', transactionLabels);
console.log('Received Data:', receivedDataPoints);
console.log('Sent Data:', sentDataPoints);
console.log('Deposit Data:', depositDataPoints);
console.log('Withdrawal Data:', withdrawalDataPoints);

// Set initial visibility of the "Total" graph to false
var initialHiddenStatus = true;


// Area Chart Example
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: accumulatedLabels,
    datasets: [
      {
        label: "Total",
        lineTension: 0.3,
        backgroundColor: "rgba(78, 115, 223, 0.05)",
        borderColor: "rgba(128, 95, 155, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(128, 95, 155, 1)",
        pointBorderColor: "rgba(128, 95, 155, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(128, 95, 155, 1)",
        pointHoverBorderColor: "rgba(128, 95, 155, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: accumulatedValues,
        hidden: initialHiddenStatus,
      },
      {
        label: "Received",
        lineTension: 0.3,
        backgroundColor: "rgba(78, 115, 223, 0.05)",
        borderColor: "rgba(78, 115, 223, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(78, 115, 223, 1)",
        pointBorderColor: "rgba(78, 115, 223, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: receivedDataPoints,
      },
      {
        label: "Sent",
        lineTension: 0.3,
        backgroundColor: "rgba(28, 200, 138, 0.05)",
        borderColor: "rgba(28, 200, 138, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(28, 200, 138, 1)",
        pointBorderColor: "rgba(28, 200, 138, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
        pointHoverBorderColor: "rgba(28, 200, 138, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: sentDataPoints,
      },
      {
        label: "Deposit",
        lineTension: 0.3,
        backgroundColor: "rgba(255, 193, 7, 0.05)",
        borderColor: "rgba(255, 193, 7, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(255, 193, 7, 1)",
        pointBorderColor: "rgba(255, 193, 7, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(255, 193, 7, 1)",
        pointHoverBorderColor: "rgba(255, 193, 7, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: depositDataPoints,
      },
      {
        label: "Withdrawal",
        lineTension: 0.3,
        backgroundColor: "rgba(255, 0, 0, 0.05)",
        borderColor: "rgba(255, 0, 0, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(255, 0, 0, 1)",
        pointBorderColor: "rgba(255, 0, 0, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(255, 0, 0, 1)",
        pointHoverBorderColor: "rgba(255, 0, 0, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: withdrawalDataPoints,
      },
    ],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'day',
          displayFormats: {
            day: 'DD/MM/YY',
        }
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 7,
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          callback: function (value, index, values) {
            return 'Ksh.' + number_format(value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function (tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ' : Ksh' + number_format(tooltipItem.yLabel);
        }
      }
    }
  }
});
