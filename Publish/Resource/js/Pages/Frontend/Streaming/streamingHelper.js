// Start laravel echo
import Pusher from "pusher-js";
// Import peer
import Peer from "simple-peer";

// Start the pushher connection object
let pusher = new Pusher("0be1b784902a6fb92390", {
    cluster: 'mt1',
});

// Export fuction asynchronously start perr
export const startPeer = async (stream = null, initiator = true) => {

    const peer1 = new Peer({ initiator: initiator, stream: stream });
    // Send the stream to the peer
    peer1.on("signal", (data) => {
        console.log(stream);
        // Send the signal to the other peer
        // const channel = pusher.subscribe("private-my-video06");
        // channel.trigger("client-02", stream);
    });

    return peer1;
};

export const recivePeer = async (stream) => {
    const peer1 = new Peer({ initiator: true, stream: stream });
    const peer2 = new Peer();

    peer1.on('signal', data => {
        peer2.signal(data)
    })

    peer2.on('signal', data => {
        peer1.signal(data)
    })

    peer2.on('stream', stream => {
        const video = document.querySelector("#video-streaming-watch");
        if ('srcObject' in video) {
            video.srcObject = stream
        } else {
            video.src = window.URL.createObjectURL(stream) // for older browsers
        }
        video.play();
    })
};
