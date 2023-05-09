import Vue from "vue";
import StarRating from 'vue-star-rating'
import Feedback from './Feedback.vue'

Vue.component('start-rating', StarRating)

new Vue({
  el: '#feedback',
  components: {Feedback}
})