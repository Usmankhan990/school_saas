import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // App is fully dynamic (per-user auth), so opt out of Cache Components.
  cacheComponents: false,
  partialPrefetching: false,
  turbopack: {
    rules: {
      "*.css": {
        loaders: ["@tailwindcss/turbopack"],
        as: "*.css",
      },
    },
  },
};

export default nextConfig;
