<template>

                <div class="card chat-box" >
                    <div class="card-header">
                        <b :class="{'text-danger':block_session}"> {{friend.name}}
                            <span v-if="block_session"> ( You are Blocked)</span>
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
                 <a class="dropdown-item" href="#" @click.prevent="Unblock"  v-if="block_session">UnBlock</a>
                     <a class="dropdown-item" href="#"   @click.prevent="block" v-else>Block</a>
                    
                     <a class="dropdown-item" href="#" @click.prevent="clear">Clear chat</a>
                    
                   </ul>
             </div>



                    
                     
                        
                   
                    <!-- Options button end-->
                     

 </div>
                <div class="card-body" v-chat-scroll>
                     <p class="card-text" :class="{'text-right':chat.type==0}" v-for="chat in chats" :key="chat.id">
                         {{chat.message}}
                           </p>

                </div>
                  <form  class="card-footer" @submit.prevent="send">
                <div class="form-group">
                <input type="text" class="form-control" placeholder="Write your message here" 
                :disabled="block_session" v-model="message">

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
            block_session:false,
            message:null
            
        }
    },
        methods:{
            send(){
              if(this.message){
                 this.pushToChats(this.message)
                  axios.post(`/send/${this.friend.session.id}`,{
                      content:this.message,
                      to_user:this.friend.id
                  });
                  this.message= null
              }

            },
            pushToChats(message){
                 this.chats.push({message: message,type:0,sent_at:'Just Now'})
            },
            
            close(){
                this.$emit('close');
            },
            clear(){
                this.chats=[]
            },
            block(){
                this.block_session=true;
                console.log(this.block_session)
            },
            Unblock(){
                 this.block_session=false;
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
           
          Echo.private(`Chat.${this.friend.session.id}`).listen("PrivateChatEvent",(e)=>{
             // console.log(e)
              this.read();
              this.chats.push({message: e.content,type:1,sent_at:'Just Now'})
          });
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