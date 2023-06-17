<template>
  <div>
        <template v-for="(item, index) in reactions">
          <font-awesome-icon :key="index" :icon="['far', `face-grin-${item.type}`]" :class="[`face-${item.type}`, 'animated']" type='button' :style="{left: `${item.left}%`, transform: `rotate(${item.rotate}deg)`} " size='2x' bounce />
        </template>
    </div>
</template>

<script>
const randomIntFormInterval = (min,max) => Math.floor(Math.random() * (max - min + 1) + min);
export default {
  data(){
    return {
      count: 0,
      reactions: {}
    }
  },

  methods: {
    displayReceivedReaction(type) {
      const i = ++this.count;

      this.reactions[i] = {
        type,
        left: randomIntFormInterval(40, 60),
        rotate: randomIntFormInterval(-30, 30)
      };

      this.$forceUpdate();

      setTimeout(() => {
        delete this.reactions[i];
        this.$forceUpdate();
      }, 2000);
    }
  }
}

</script>


<style scoped>

</style>