// Minimal service worker: makes the app installable. Always goes to the network
// (school data must be fresh); shows a small offline notice when there is none.
self.addEventListener("install", () => self.skipWaiting());
self.addEventListener("activate", (e) => e.waitUntil(self.clients.claim()));
self.addEventListener("fetch", (event) => {
  if (event.request.mode !== "navigate") return;
  event.respondWith(
    fetch(event.request).catch(
      () =>
        new Response(
          "<!doctype html><meta name=viewport content='width=device-width,initial-scale=1'><body style='font-family:system-ui;display:grid;place-items:center;height:100vh;margin:0'><p>You are offline. Reconnect to use EduVerse.</p>",
          { headers: { "Content-Type": "text/html" } },
        ),
    ),
  );
});
