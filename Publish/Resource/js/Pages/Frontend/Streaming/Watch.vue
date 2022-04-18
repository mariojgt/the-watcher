<template>
  <Layout>
    <div class="hero min-h-screen bg-base-200">
      <div class="hero-content flex-col lg:flex-row-reverse">
        <video
          class="max-w-screen-lg rounded-lg shadow-2xl"
          id="video-streaming-watch"
          controls
        ></video>

        <button @click="startWatch">Start</button>
      </div>
    </div>
  </Layout>
</template>

<script setup>
// Import axios
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";
import { onMounted } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
// Incluse the layout based in the root path
import Layout from "../../../Layout/Frontend";

// Import the from components
import InputField from "@mariojgt/masterui/packages/Input/index";
import InputPassword from "@mariojgt/masterui/packages/Password/index";
import Submit from "@mariojgt/masterui/packages/Submit/index";
import LinkButton from "@mariojgt/masterui/packages/Link/index";

// Streaming helper
import { recivePeer } from "./streamingHelper";

// Start laravel echo
import Pusher from "pusher-js";
// Start the pushher connection object
let pusher = new Pusher("0be1b784902a6fb92390", {
  cluster: "mt1",
});

let isLive = $ref(false);

const props = defineProps({
  title: {
    type: String,
    default: "mariojgt is heredude",
  },
});

// Next video key
let keyCounter = $ref(0);
// Time video has been playing
let videoTimer = $ref(0);

// Start sharing my screen
const startWatch = async () => {
  // Play the video
  playSyncVideo();

  const video = document.getElementById("video-streaming-watch");
  // Set a event on the video is ended so we can fetch the new video data
  video.addEventListener("ended", () => {
    console.log("ended");
    // Get the time the video has been playing
    const videoTimer = video.currentTime;
    playSyncVideo(videoTimer);
  });
};

const playSyncVideo = async (startAtt = null) => {
  keyCounter++;
  // Find the video element
  const video = document.getElementById("video-streaming-watch");
  // Change the src tag to the stream
  video.src = route("streaming.video.watch", keyCounter);
  // Start the video at the time the video has been playing
  if (startAtt) {
    video.currentTime = startAtt;
    // stop the video
    video.pause();
  } else {
    // Start the video
    video.play();
  }
  return video;
};

// Open the stream to the channel
//   const channel = pusher.subscribe("private-my-video06");

//   channel.bind("client-02", function (data) {
//     // Make sure is a media stream
//     console.log(data);
//     // recivePeer(data);
//   });
</script>
