<template> 
    <div class="container">

        <button @click="update(data.delta, 'back')" class="btn btn-outline-primary me-3">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
            <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-left" viewBox="0 0 16 16">
  <path d="M10 12.796V3.204L4.519 8 10 12.796zm-.659.753-5.48-4.796a1 1 0 0 1 0-1.506l5.48-4.796A1 1 0 0 1 11 3.204v9.592a1 1 0 0 1-1.659.753z"/>
</svg> Назад </button>
        <strong>
            <span v-if="data.text=='last_24'" class="ps-xxl-10">За последниие 24 часа</span>
            <span v-else>{{data.text}}</span>
        </strong>
        <button @click="update(data.delta, 'next')" class="btn btn-outline-primary ms-3" v-if="data.delta!=0"> Вперед 
            <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right" viewBox="0 0 16 16">
  <path d="M6 12.796V3.204L11.481 8 6 12.796zm.659.753 5.48-4.796a1 1 0 0 0 0-1.506L6.66 2.451C6.011 1.885 5 2.345 5 3.204v9.592a1 1 0 0 0 1.659.753z"/>
</svg><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="loading"></span>
        </button>


        <line-chart-down :chart-data="data.graf" :options="opt" :height="150"></line-chart-down>
        <h2>Количество машин в очереди</h2>
        <bar-chart :chart-data="data.bar" :options="opt2" :height="150"></bar-chart>
    </div>
</template>

<script>
import { bus } from './Bus'
import LineChartDown from './LineChart.js'
import BarChart from './BarChart.js'
import axios from 'axios'

    export default {
        props: {
        border_id: {
        type: String,
        default: '',
    },
    },
        name: 'LineChartUp',
        components: {
            LineChartDown,
            BarChart
        },
        data() {
            return {
                data: [],
                opt : {},
                opt2 : {},
                arrow:'',
                loading:true

            }
        },
        mounted() {
            this.run()
        },
        methods: {
            run: function (delta=0) {
                console.log(delta);
                this.opt = {
                        responsive: true,
                        maintainAspectRatio: true,
                        legend: {
                            display: false 
                        },
                        scales: {
                            yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                            }]
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 0,
                                bottom: 0
                            }
                        }
                    }
                    this.opt2 = {
                        responsive: true,
                        maintainAspectRatio: true,
                        legend: {
                            display: false 
                        }
                    }
                axios.get('/ee/data-chart/'+this.border_id+'/'+delta).then((response) => {
                    this.data=response.data

                console.log('test');
                console.log(this.data.bar.datasets[0].data[0])
                let nvlArr = this.data.bar.datasets[0].data.slice(-3).map(Number)
                nvlArr.reverse()

                console.log('test2');
                
                if(Math.max.apply(null, nvlArr)<60) {
                    this.arrow = 'ntrl'
                }
                else {
                    if(nvlArr[0]>(nvlArr[1]+nvlArr[2])/2) this.arrow = 'up'
                    else this.arrow = 'down'
                }
                bus.$emit('mega-event',this.arrow);
                this.loading = false
                });
                
            },
            update: function (delta=0, act) {
                this.loading=true
                if (act=='back') delta++;
                if (act=='next') delta--;
                console.log(delta);
                axios.get('/ee/data-chart/'+this.border_id+'/'+delta).then((response) => {
                    this.data=response.data
                    this.loading=false
                });
            },
            downdate: function (delta=0) {
                delta--;
                console.log(delta);
                axios.get('/ee/data-chart/'+this.border_id+'/'+delta).then((response) => {
                    this.data=response.data
                });
            }
        }
    }
</script>
