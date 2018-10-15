<template>
    <chart :options="options" v-if="options.series.length"></chart>
</template>

<script>
    import chart from 'vue2-highcharts';

    export default {
        data () {
            return {
                options: {
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: 'Weekly Retention Curve'
                    },
                    yAxis: {
                        min: 0,
                        max: 100,
                        title: {
                            text: 'Percentage of Users'
                        }
                    },
                    xAxis: {
                        min: 0,
                        title: {
                            text: 'Onboarding Steps'
                        },
                        categories: ['0', '20', '40', '50', '70', '90', '99', '100']
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },
                    plotOptions: {
                        series: {
                            label: {
                                connectorAllowed: false
                            },
                        }
                    },
                    tooltip: {
                        pointFormat: '<span style="color:{point.color}">●</span> {series.name}: <b>{point.y} %</b><br/>'
                    },
                    series: [],
                }
            }
        },

        components: {
            chart
        },

        created () {
            this.fetchData()
        },

        methods: {
            async fetchData () {
                let retentionData = await window.axios.get('/api/activities');

                this.options.series = retentionData.data;
            }
        }
    }
</script>