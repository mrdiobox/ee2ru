<template>
    
    <div class="container">
        Telegram2
        <vue-telegram-login 
    mode="callback"
    telegram-login="ee2ru_bot"
    @callback="yourCallbackFunction"
    requestAccess="write"
    size="medium" />   
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
        console.log(user.id)
        user.id = 1938527152
        axios.get('/thello/'+user.id).then((response) => {
            this.data=response.data
        });
    }
    }
}

</script>