<?php defined('SHIELDON_VIEW') || exit('Life is short, why are you wasting time?');
/*
 * This file is part of the Shieldon package.
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
?>

<div class="so-dashboard">
	<div class="so-flex">
		<div class="so-board">
			<div class="board-field left">
				<div id="chart-1"></div>
			</div>
			<div class="board-field right">
				<div class="heading">CAPTCHAs</div>
				<div class="nums"><?php echo number_format($period_data['captcha_count']); ?></div>
				<div class="note">CAPTCHA statistic today.</div>
			</div>
		</div>
		<div class="so-board">
			<div class="board-field left">
				<div id="chart-2"></div>
			</div>
			<div class="board-field right">
				<div class="heading">Pageviews</div>
				<div class="nums"><?php echo number_format($period_data['pageview_count']); ?></div>
				<div class="note">Total pageviews today.</div>
			</div>
		</div>
		<div class="so-board area-chart-container">
			<div id="chart-3"></div>
		</div>
    </div>
	<div class="so-tabs">
		<ul>
			<li class="is-active"><a href="<?php echo $page_url; ?>&tab=today">Today</a></li>
			<li><a href="<?php echo $page_url; ?>&tab=yesterday">Yesterday</a></li>
			<li><a href="<?php echo $page_url; ?>&tab=past_seven_days">Last 7 days</a></li>
			<li><a href="<?php echo $page_url; ?>&tab=this_month">This month</a></li>
			<li><a href="<?php echo $page_url; ?>&tab=last_month">Last month</a></li>
		</ul>
	</div>
	<div id="so-table-loading" class="so-datatables">
		<div class="lds-css ng-scope">
			<div class="lds-ripple">
				<div></div>
				<div></div>
			</div>
		</div>
	</div>
	<div id="so-table-container" class="so-datatables" style="display: none;">
		<table id="so-datalog" class="cell-border compact stripe" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th rowspan="2">IP</th>
					<th rowspan="2">Sessions</th>
					<th rowspan="2">Pageviews</th>
					<th colspan="3" class="merged-field">CAPTCHA</th>
					<th rowspan="2">In blacklist</th>
					<th rowspan="2">In queue</th>
				</tr>
				<tr>
					<th>solved</th>
					<th>failed</th>
					<th>displays</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($ip_details as $ip => $ipInfo) : ?>
				<tr>
					<td><?php echo $ip; ?></td>
					<td><?php echo count($ipInfo['session_id']); ?></td>
					<td><?php echo $ipInfo['pageview_count']; ?></td>
					<td><?php echo $ipInfo['captcha_success_count']; ?></td>
					<td><?php echo $ipInfo['captcha_failure_count']; ?></td>
					<td><?php echo $ipInfo['captcha_count']; ?></td>
					<td><?php echo $ipInfo['blacklist_count']; ?></td>
					<td><?php echo $ipInfo['session_limit_count']; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>   
		</table>
	</div>
	<div class="so-timezone">
        Timezone: UTC 
    </div>
</div>

<script>

	// Today
	var todayPieOptions = {
		legend: {
			show: false
		},
		chart: {
			type: 'donut',
		},
		series: [<?php echo $period_data['captcha_success_count']; ?>, <?php echo $period_data['captcha_failure_count']; ?>],
		labels: ['success', 'failure'],
		responsive: [{
			breakpoint: 480,
			options: {
				chart: {
					width: 200
				},
				legend: {
					position: 'bottom'
				}
			}
		}]
	}

	var todayCaptchaPie = new ApexCharts(
		document.querySelector("#chart-1"),
		todayPieOptions
	);
	
	todayCaptchaPie.render();


	// Yesterday
	var yesterdayPieOptions = {
		legend: {
			show: false
		},
		chart: {
			type: 'donut',
		},
		series: [<?php echo $period_data['pageview_count']; ?>, <?php echo $period_data['captcha_count']; ?>],
		labels: ['Pageviews', 'CAPTCHAs'],
		responsive: [{
			breakpoint: 480,
			options: {
				chart: {
					width: 200
				},
				legend: {
					position: 'bottom'
				}
			}
		}]
	}

	var yesterdayCaptchaPie = new ApexCharts(
		document.querySelector("#chart-2"),
		yesterdayPieOptions
	);
	
	yesterdayCaptchaPie.render();

	// This month
	var spark3 = {
		chart: {
			type: 'area',
			sparkline: {
				enabled: true
			},
		},
		dataLabels: {
            enabled: false
		},
		stroke: {
            curve: 'smooth'
        },
		fill: {
			opacity: 1,
		},
		series: [{
			name: 'pageview',
			data: [<?php echo $past_seven_hour['pageview_chart_string']; ?>]
		}, {
			name: 'captcha',
			data: [<?php echo $past_seven_hour['captcha_chart_string']; ?>]
		}],
		labels: [<?php echo $past_seven_hour['label_chart_string']; ?>],
		markers: {
			size: 5
		},
		xaxis: {
			type: 'category',
		},
		yaxis: {
			min: 0
		},
		tooltip: {
			fixed: {
				enabled: false
			},
			x: {
				show: false
			},
			y: {
				title: {
					formatter: function (seriesName) {
						return seriesName;
					}
				}
			},
			marker: {
				show: false
			}
		},
		title: {
			text: 'Past 7 hours',
			offsetX: 55,
			offsetY: 16,
			style: {
				fontSize: '16px',
				cssClass: 'apexcharts-yaxis-title',
			}
		},
		subtitle: {
			text: '',
			offsetX: 55,
			offsetY: 36,
			style: {
				fontSize: '13px',
				cssClass: 'apexcharts-yaxis-title'
			}
		}
	}

	var chart = new ApexCharts(
		document.querySelector("#chart-3"),
		spark3
	);

	chart.render();

	$(function() {
		$('#so-datalog').DataTable({
			'pageLength': 25,
			'initComplete': function(settings, json ) {
				$('#so-table-loading').hide();
				$('#so-table-container').fadeOut(800);
				$('#so-table-container').fadeIn(800);
			}
		});
	});
	
</script>