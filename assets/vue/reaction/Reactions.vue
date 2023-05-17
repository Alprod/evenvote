<template>
  <ul>
    <li @click="addReaction('heaths')">Coeur</li>
    <li @click="addReaction('starts')">Start</li>
    <li @click="addReaction('tears')">Tears</li>
  </ul>
</template>

<script>
import Vue from "vue";
import ReceivedReaction from "./ReceivedReaction.vue";

export default {
  props: {sessionId: { type: String, required: true } },
  data() {
    return {
      stars: 0,
      heaths: 0,
      tears: 0,
      reactions: []
    }
  },

  created() {
    const RR = Vue.extend(ReceivedReaction);
    const vm = new RR;
    vm.$mount();
    document.body.appendChild(vm.$el);

    const session = `/api/session/${this.sessionId}`;

    fetch(`${session}/reaction`)
        .then(resp => resp.json().then(data => ({
          data,
          hubUrl: resp.headers.get('Link').match(/<([^>]+)>;\s+rel="[^"]*mercure[^"]*"/)[1]
        })) )
        .then(({data, hubUrl}) => {
          Object.entries(data).forEach( ([type, nb]) => this[type] = nb );
          const es = new EventSource(`${hubUrl}?topic=${document.location.origin}/api/reactions/{id}`);
          console.log(es);
          es.onmessage = ({data}) => {
          const reaction = JSON.parse(data);
          if(reaction.session !== session ) return;

          ++this[reaction.type];
          vm.displayReceivedReaction(reaction.type)
          }
        })
  },

  methods: {
    addReaction(type){
      fetch('/api/reactions', {
        method: 'POST',
        headers: {'Content-Type': 'application/ld+json'},
        body: JSON.stringify({session: `/api/sessions/${this.sessionId}`, type})
      })
          .then(({ok, statusText}) => {
        console.log(ok)
        if(!ok) alert(statusText)
      })
    }
  }

}

</script>


<style scoped>

</style>