<template>
    
    <div class="container">

        <div v-if="authStatus === '2'">
            {{ user.first_name }} <a href="#" @click="logout()">Выйти</a>
            <form class="review-form" @submit.prevent="onSubmit">
                <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">B-</span>
  <input type="text" class="form-control" v-model="number" v-mask="'##-##-####'" placeholder="00-00-0000" id="number">
  <input type="submit" value="Следить">                
</div>
            

              
            </form>
        </div>
        <div v-else-if="authStatus === '1'">
            {{ user.first_name }} : {{ number }}
            <form class="review-form" @submit.prevent="onDelete">
            <input type="submit" value="Удалить">
            </form>
        </div>
        <div v-else>
            <vue-telegram-login 
            mode="callback"
            telegram-login="ee2ru_bot"
            @callback="yourCallbackFunction"
            requestAccess="write"
            size="medium" /> 
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
        this.yourCallbackFunction(this.user)
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
       
        user.id =1938527152,
        user.first_name ="Андрей",
        user.last_name ="Громов",
        user.username ="mrdiobox",
        user.photo_url ="https://t.me/i/userpic/320/A3wuI20V1XnY-CHe3mDsFdVDFPILpLg6AaQDMIBK4qo.jpg",
        user.auth_date = 1673443320,
        user.hash="b781ed1f45972c565df1ce2b8bf5f04f638f050da934bc8a7b39bd83fba29954"

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