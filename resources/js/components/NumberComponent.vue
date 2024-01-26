<template>
<div class="container">
<ul class="list-group">
  <li class="list-group-item" v-for="num in nums">
    {{ num }}
  </li>
</ul>
<br>
</div>
</template>

<script>
import axios from 'axios'
export default {
  props: {
   border_id: {
       type: String,
       default: '',
   },
},
    data() {
            return {
                nums:[]
            }
    },
    mounted() {
        
        axios.get('/ee/cars/'+this.border_id).then((response) => {
                    this.nums=response.data.cars
                    console.log(response.data.cars)
                    console.log(this.nums)
        })

        
    }
}

</script>