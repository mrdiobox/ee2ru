<template>
    
    <div class="container">
        Telegram2
        <vue-telegram-login 
    mode="callback"
    telegram-login="ee2ru_bot"
    @callback="yourCallbackFunction" />   
    </div>
    </template>

<script>
import axios from 'axios'
import {vueTelegramLogin} from 'vue-telegram-login'
export default {
    components: {vueTelegramLogin},
    data() {
            return {
                nums:[]
            }
    },
    mounted() {
        
    },
    methods: {
        yourCallbackFunction (user) {

      console.log(user)
    }
    }
}

</script>