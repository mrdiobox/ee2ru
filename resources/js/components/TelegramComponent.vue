<template>
    
    <div class="container">

        <div v-if="authStatus === '2'">
             
            <form class="review-form" @submit.prevent="onSubmit">

  
  <div class="card">
  <h5 class="card-header">Здравствуйте, {{ user.first_name }} / <a href="#" @click="logout()">Выйти</a></h5>
  <div class="card-body">
    <h5 class="card-title">Введите номер вашей очереди для получения уведомлений в Telegram:</h5>
    <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">B-</span>
  <input type="text" class="form-control" v-model="number" v-mask="'##-##-####'" placeholder="00-00-0000" id="number">
  <input type="submit" value="Следить" class="btn btn-outline-primary me-3">  
  </div>
  </div>
</div>


            

              
            </form>
        </div>
        <div v-else-if="authStatus === '1'">
            <div class="card">
  <h5 class="card-header">{{ user.first_name }} : {{ number }}</h5>
  <div class="card-body">
    <h5 class="card-title">Когда вас вызовут на границу, вы получите уведомление в Telegram</h5>
            <ul class="list-group">
                <li class="list-group-item active">                
                    <form class="review-form" @submit.prevent="onDelete">
                         Ваш номер очереди: <b>{{ number }}</b> <input type="submit" value="Остановить отслеживание" class="btn btn-warning mx-1 my-2">
                    </form>
                 </li>
            </ul>
            </div>
            </div>
 
        </div>
        <div v-else>
            <div class="card">
  <h5 class="card-header">
    <vue-telegram-login 
            mode="callback"
            telegram-login="ee2ru_bot"
            @callback="yourCallbackFunction"
            requestAccess="write"
            size="medium" /> 
  </h5>
  <div class="card-body">
          Чтобы отслеживать вашу очередь и получать уведомления, войдите с помощью Telegram.
            </div>
            </div>
        </div>

 
</div>
    </template>

<script>
import axios from 'axios'
import {vueTelegramLogin} from 'vue-telegram-login'
import VueMask from 'v-mask'
Vue.use(VueMask);
export default {
    components: {vueTelegramLogin},
    data() {
            return {
                number:'',
                user: {},
                tform: true,
                token:'',
                params: {},
                authStatus: '-1',
                myInputModel: ''
            }
    },
    mounted() {
       // this.yourCallbackFunction(this.user)
        this.ifLogin()
    },
    methods: {
        logout: function() {
            console.log('LOGOut')
            axios.get('/logout').then((response) => {
            console.log(response.data)
            if (response.data.status) {
                this.authStatus = response.data.status
                user = Object()
                number = ''
            }

        });
        },
        ifLogin: function() {
            axios.get('/iflogin').then((response) => {
            console.log('ifLogin:')
            console.log(response.data)
            if (response.data.status) {
                console.log('AS: '+response.data.status)
                this.authStatus = response.data.status
                this.user.first_name = response.data.data.username
                if(response.data.data.number) {
                    console.log('ifNumber:')
                    this.number = response.data.data.number
                    this.user.first_name = response.data.data.username
                }
            }
        });
        },
        onSubmit: function() {
            let params = {'number' : this.number }
            axios.post('/addnumber', params).then((response) => {
            console.log(response.data)
            if (response.data.status) {
                this.authStatus = response.data.status
                this.user.first_name = response.data.data.username
            }

        });
        },
        onDelete: function() {
            axios.get('/removenumber').then((response) => {
            console.log(response.data)
            if (response.data.status) {
                this.number = ''
                this.authStatus = response.data.status
            }

        });
        },
        yourCallbackFunction: function (user) {
        //let user=Object
 /*      
        user.id =1938527152,
        user.first_name ="Андрей",
        user.last_name ="Громов",
        user.username ="mrdiobox",
        user.photo_url ="https://t.me/i/userpic/320/A3wuI20V1XnY-CHe3mDsFdVDFPILpLg6AaQDMIBK4qo.jpg",
        user.auth_date = 1673796383,
        user.hash="8993906a45850bdf7264f96e7d0883dfc60fceb5e13df03c05ba8817349b9227"
*/
       // console.log(user)
        //console.log(user.id)
        axios.post('/tauth', user).then((response) => {
            console.log('test')
            console.log(response.data)
            if (response.data.status == '2') {
                this.authStatus = response.data.status
                this.user.first_name = response.data.data.username
            }
        });
    }
    }
}

</script>