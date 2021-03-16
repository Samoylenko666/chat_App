<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Private chat application</div>

             
   
                  
                  <ul class="list-group">
                     
                          
          <li class="list-group-item" @click.prevent="openChat(friend)" v-for="friend in friends" :key=friend.id >
              
               <a href="" >
               {{friend.name}}  
               
               <span class="text-danger" v-if="friend.session && (friend.session.unreadCount>0)">{{ friend.session.unreadCount}}</span>
               </a>


               <i class="fas fa-circle float-right text-success" v-if="friend.online"></i>
               
               </li>

                      
                 
                  
                  </ul>
                 

            
                </div>
            </div>

        <div class="col-md-9">  
          
            <span v-for="friend in friends" :key="friend.id" > 
                <span v-if="friend.session">   <!--   Вот v-if and v-for не работают вместе -->

          
                
                <message-component 
                 v-if="friend.session.open"  
                
                 @close="close(friend)" :friend=friend>   <!-- кнопка close  -->
                 <!-- вот здесь friend  это пропс для message component -->
                </message-component>

            </span> </span>
                
        </div>
        </div>
    </div>
</template>

<script>
import MessageComponent from './MessageComponent';
    export default {
        data(){
        return{
            //open:true,
            friends:[]
        }
    },
        methods:{
            close(friend){
                friend.session.open=false
            },

            getFriends(){
                axios.post('/getFriends').then(res=>{
                    this.friends= res.data.data  
                    this.friends.forEach(friend=>(friend.session?this.listenForEverySession(friend):""));
                });
            },

            openChat(friend){
                if(friend.session){
                    this.friends.forEach(friend=>{
                        if(friend.session){ //прохожу по всему массиву где есть сессия        
                    friend.session.open=false //и делаю open false для всех сообщений, так как должно только одно                                       
                }                             // показываться на экране                                  
                });
                friend.session.open =true //сообщения с текущим юзером остаются открытыми
                friend.session.unreadCount =0
                }else{
                    //create session
                this.createSession(friend);
                
                }
            },
            createSession(friend){// Метод для создания сессии
            axios.post('/session/create',{friend_id:friend.id}).then(res=>{ 
                (friend.session= res.data.data),
                (friend.session.open=true) ;
        });
        },
        listenForEverySession(friend){
            //Слушается событие которое считает количество не прочитаных сообщений
             Echo.private(`Chat.${friend.session.id}`)
                          .listen("PrivateChatEvent",(e)=> friend.session.open?"":friend.session.unreadCount++
              
          );
        }
           
        },
       
        created(){
           // this.$on('close',()=>this.close()); //  вызывается функция close когда из Другого компонета запустится функция с emit
        this.getFriends();
        

      
           //для обычного канала  не нужна авторизация
           //он выводит всех пользователей которые онлайн
        // Слушается события которое передает что кто-то создал сессию между текущим юзером
        Echo.channel('Chat').listen('SessionEvent',e=>{
            let friend= this.friends.find(friend=> friend.id==e.session_by);
            friend.session=e.session;
            //запускается счетчик непрочитаных сообщений
            this.listenForEverySession(friend);
        });
       //Еcho  для мониторинга онлайн/офлайн юзеров
        Echo.join('Chat')
        .here((users)=>{
            this.friends.forEach(friend=>{
                users.forEach(user=>{if(user.id==friend.id){
                    friend.online =true 
                }
                })
            })
        }) // Если юзер присоеденился/зашел на эту страницу его статус становится онлайн
      .joining((user) => {
        this.friends.forEach(friend=>user.id==friend.id? friend.online=true:'')
      }) // Если юзер покинул страницу его статус становится оффлайн
       .leaving((user) => {
         this.friends.forEach(friend=>user.id==friend.id? friend.online=false:'')
    });


       },
        components:{
            MessageComponent
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
