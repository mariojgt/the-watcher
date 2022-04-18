<template>
  <Layout>
    <div class="hero min-h-screen bg-base-200">
      <div class="hero-content flex-col lg:flex-row-reverse">
        <video
          class="max-w-screen-lg rounded-lg shadow-2xl"
          id="video-streaming"
          autoplay
        ></video>
        <div>
          <h1 class="text-5xl font-bold">Welcome</h1>
          <p class="py-6">Bla bla bla</p>
          <button
            class="btn btn-primary"
            @click="startScreenVideo"
            v-if="isLive == false"
          >
            Go Live
          </button>
          <button class="btn btn-primary" @click="stopScreenVideo" v-else>
            Stop
          </button>
        </div>
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

// Start laravel echo
import Pusher from "pusher-js";
// Import peer
import Peer from "simple-peer";
import { startPeer } from "./streamingHelper";

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

const getPermissions = () => {
  return new Promise((resolve, reject) => {
    navigator.mediaDevices
      .getDisplayMedia({ video: true, audio: true })
      .then((stream) => {
        resolve(stream);
      })
      .catch((err) => {
        reject(err);
        //   throw new Error(`Unable to fetch stream ${err}`);
      });
  });
};

// Start sharing my screen
const startScreenVideo = async () => {
  // Get the video element
  const video = document.querySelector("#video-streaming");
  // Get permission
  let stream = await getPermissions();
  // Set the video stream to the video element
  video.srcObject = stream;
  // Play the video stream
  video.play();

  // Create the media record options to start the stream
    const mediaRecorderOptions = {
        mimeType: "video/webm",
        videoBitsPerSecond: 2500000,
        audioBitsPerSecond: 128000,
    };
  // Create the media recorder
  const mediaRecorder = new MediaRecorder(stream, mediaRecorderOptions);

  //   let constraints = {
  //     frameRate: 25,
  //     width: 1280,
  //     height: 720,
  //   };
  //   mediaRecorder.stream.getVideoTracks()[0].applyConstraints(constraints);

  // Start the media recorder
  mediaRecorder.start(1000); // 1000 - the number of milliseconds to record into each Blob
  mediaRecorder.ondataavailable = (event) => {
    // Create a new blob
    const file = new File([event.data], "video.webm");

    // Form data
    const formData = new FormData();
    formData.append("file", file);
    // Send the file to the server using axios as video
    axios
      .post(route("streaming.send"), formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      .then((res) => {
        console.log(res);
      })
      .catch((err) => {
        console.log(err);
      });
    // axios.post(route("streaming.send"), {
    //   data: event.data,
    // });
  };

  // Start the peer connection
  // const peer = startPeer(stream);
  isLive = true;
};

// Stop sharing my screen
const stopScreenVideo = () => {
  // Get the video element
  const video = document.querySelector("#video-streaming");
  // Stop the video stream
  video.srcObject.getTracks().forEach((track) => track.stop());
  isLive = false;
};
</script>
