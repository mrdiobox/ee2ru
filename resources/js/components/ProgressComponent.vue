<template>
    <div class="container">
    <vue-ellipse-progress  :data="data">
        <p slot="legend-caption" class="progDelay">{{frt}} <span v-if="data[1].progress">/ {{frt+1}}</span>
          <svg v-if="arrow == 'up'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-arrow-up-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 0 0 8a8 8 0 0 0 16 0zm-7.5 3.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11.5z"/>
</svg>
<svg v-else-if="arrow == 'down'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-arrow-down-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/>
</svg>
        </p>
    </vue-ellipse-progress>
    <strong>
    <p v-show="sh">Сейчас:
      <span v-if="frt == 0">без задержки.</span>
      <span v-else>задержка {{frt}} часов.</span>
      <br>
      <span v-if="prog2"> Возможно увеличение времени ожидания.</span>
      <span v-if="arrow == 'up'"> Очередь растет.</span>
      <span v-else-if="arrow == 'down'"> Очередь уменьшается.</span>
    </p>
  </strong>
    </div>
</template>
 
<script>
import { bus } from './Bus'
import { tttt } from "vue-ellipse-progress";
import axios from 'axios'
import { bottom } from '@popperjs/core';

export default {
  props: {
   border_id: {
       type: String,
       default: '',
   },
},
  components: { tttt, bus },
  data: function() {
            return {
                data: [ ],
                frt : '',
                arrow:'',
                sh: false,
                prog2:0
            }
        },
  created() {
     this.run()
   },
   mounted () {
            // Как-то обрабатываем данные
            
        },
  methods: {
    run: function () {      

                let roundColor = ['#65B031', '#D1EA2C','#FFFE34', '#F9BC02', '#FA9801', '#FE2713','#A7194B', '#6F1032', '#370819', '#000098', '#000065'];
                let color1 = '#f6e9f0'
                let color2 = 'black'
                this.data =   [{ 
                    progress: 100, // required for each circle
                    color: color1,  // will overwrite global progress color
                    thickness: 20,
                    loading: true
                  },
                  {
                    progress: 100,
                    loading: true
                  }
                ]

                axios.get('/ee/progress/'+this.border_id).then((response) => {

                  if (response.data.frt >= roundColor.length-1) {
                    color1 = roundColor.slice(-1)
                    color2 = roundColor.slice(-2)
                  }
                  else {
                    color1 = roundColor[response.data.frt]
                    color2 = roundColor[response.data.frt+1]
                  }
                  this.data =   [{ 
                    progress: 100, // required for each circle
                    color: color1,  // will overwrite global progress color
                    thickness: 20,
                    loading: false
                  },
                  { 
                    progress: response.data.progress2, // required for each circle
                    color: color2,
                    lineMode:bottom,
                    loading: false
                  }
                ]
                this.sh=true
                this.prog2 = response.data.progress2
                this.frt = response.data.frt  
                console.log(this.data[1].progress);
                });
                bus.$on("mega-event", (name)=>{
                  console.log(name)
                  this.arrow = name
                });
            }
  }
  
};

</script>

<style>
.svg_name {
  fill: red;
}
.progDelay {
 font-size: 40px; 
}
.progDelay span {
 font-size: 50%; 
}
</style>