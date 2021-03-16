<template>

                <div class="card chat-box" >
                    <div class="card-header">
                        <b :class="{'text-danger':session.block}">
                             {{friend.name}} <span v-if="isTyping">is Typing...</span>
                            <span v-if="session.block"> ( You are Blocked)</span>
                             </b>
                         

                     


                            <!--  метод close from vue -->
                        <a href="" @click.prevent="close">  
                       <i class="fa fa-times float-right"></i>   <!-- картинка загружается с сайта fa icons через cdn -->      
                             </a>
                             <!-- close end -->

                            <!-- Options button -->
               <div class="dropdown float-right mr-4 ">
  
                         <a href="" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-ellipsis-v "></i>
                        </a>  
                   <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                 <a class="dropdown-item" href="#" @click.prevent="Unblock"  v-if="session.block && can">UnBlock</a>
                     <a class="dropdown-item" href="#"   @click.prevent="block" v-if="!session.block">Block</a>
                    
                     <a class="dropdown-item" href="#" @click.prevent="clear">Clear chat</a>
                    
                   </ul>
             </div>



                    
                     
                        
                   
                    <!-- Options button end-->
                     

 </div>
                <div class="card-body" v-chat-scroll>
                     <p class="card-text" :class="{'text-right':chat.type==0,
                      'text-success':chat.read_at!=null}" v-for="chat in chats" :key="chat.id">
                         {{chat.message}}
                         <br>
                         <span style="font-size:10px">{{ chat.read_at}}</span>
                           </p>

                </div>
                  <form  class="card-footer" @submit.prevent="send">
                <div class="form-group">
                <input type="text" class="form-control" placeholder="Write your message here" 
                :disabled="session.block" v-model="message">

                </div>
              
                   
                </form>

                </div>    
</template>


<script>

export default {
    props:['friend'],
    data(){
        return{
            chats:[],
            message:null,
            isTyping: false
            
        }
    },
    computed:{
        //Создается session которая типа как переменая/ниже обращаюсь просто this.session.blockХпример)
        // Получается к session приравниввается friend.session
        session(){
            return this.friend.session;
        },
        can(){
        // через window получаю переменную auth из другого html документа
        return this.session.blocked_by== auth.id// Возвращает true or false
    }
    },
    watch:{
        message(value){
            //Когда юзер пишет сообщения слушается событие, которое передает имя кто набирает сообщение
        Echo.private(`Chat.${this.friend.session.id}`)
       .whisper('typing', {
        name: auth.name
    });
        }
    },
    
        methods:{
            send(){
              if(this.message){
                 this.pushToChats(this.message)
                  axios.post(`/send/${this.friend.session.id}`,{
                      content:this.message,
                      to_user:this.friend.id
                  })
                  .then(res=> this.chats[this.chats.length -1] // последний элемент массива
                  .id=res.data);
                  this.message= null
              }

            },
            pushToChats(message){
                 this.chats.push({
                     message: message,
                     type:0, 
                     read_at:null,
                     sent_at:'Just Now'})
            },
            
            close(){
                this.$emit('close');
            },
            clear(){
                //this.chats=[];
                axios.post(`/session/${this.friend.session.id}/clear`)
                .then(res=> (this.chats=[]))
            },
            block(){
                this.session.block=true;
                axios.post(`/session/${this.friend.session.id}/block`)
                .then(res=> (this.session.blocked_by= auth.id) )
            },
            Unblock(){
                 this.session.block=false;
                 axios.post(`/session/${this.friend.session.id}/unblock`)
                .then(res=> (this.session.blocked_by= null))
            },
            getAllMessage(){
             axios.post(`/session/${this.friend.session.id}/chats`)
            .then(res=> this.chats= res.data.data);
            },
            read(){
                axios.post(`/session/${this.friend.session.id}/read`)

            }
        },
        created(){
          this.getAllMessage();
          this.read();
           
           // Вся хуйня котороая в Echo обновлятся асинхронно(Без перезагрузки страницы)
           //для private канала нужна авторизация
          //Слушается событие если юзеру отправили сообщения в массив чат добавляется сообщение от юзера
          Echo.private(`Chat.${this.friend.session.id}`).listen("PrivateChatEvent",(e)=>{
              //Если юзер  открыл сообщение тогда вызывается метод read() который делает все отправленные сообщения прочитаными
              //для  юзера который их отправил
              this.friend.session.open ? this.read() : "";
              this.chats.push({message: e.content,type:1,sent_at:'Just Now'})
          });

          //Когда юзер прочитает сообщения переменная read_at принимает значение
          Echo.private(`Chat.${this.friend.session.id}`).listen("MsgReadEvent",  
          e=>this.chats.forEach(chat=>chat.id== e.chat.id ? chat.read_at=e.chat.read_at : ""  )
          );
          //Если юзера заблокируют слушается событие и переменная блок становится тру
          Echo.private(`Chat.${this.friend.session.id}`).listen("BlockEvent",  
          e=>this.session.block= e.blocked
          );

            //Слушается событие если кто то пишет сообщение, переменная истайпинг принимает тру 
             Echo.private(`Chat.${this.friend.session.id}`)
            .listenForWhisper('typing',
             (e) => {this.isTyping= true
             //Задержка в 2секунду после которой истайпинг становится снова false
                setTimeout(() => {
                    this.isTyping= false
                }, 2000);
             }
            );

        }
            
}
</script>


<style>

.chat-box{
    height: 400px ;
 
}

.card-body{
    overflow-y: scroll; /* Добавлет скролл */
}
</style>