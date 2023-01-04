<template>
    
    <div class="container">
        Telegram1
        <script type="application/javascript">
  function onTelegramAuth(user) {
    alert('Logged in as ' + user.first_name + ' ' + user.last_name + ' (' + user.id + (user.username ? ', @' + user.username : '') + ')');
  }
</script>
        <script type="application/javascript" async src="https://telegram.org/js/telegram-widget.js?21" data-telegram-login="ee2ru_bot" data-size="medium" data-onauth="onTelegramAuth(user)" data-request-access="write"></script>
    </div>
    </template>

<script>
import axios from 'axios'
export default {
    data() {
            return {
                nums:[]
            }
    },
    mounted() {
        
    },
    methods: {
        onTelegramAuth: function (user) {
            alert('Logged in as ' + user.first_name + ' ' + user.last_name + ' (' + user.id + (user.username ? ', @' + user.username : '') + ')');
        }
    }
}

</script>