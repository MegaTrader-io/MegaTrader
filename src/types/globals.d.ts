export {};

declare global {
    interface Window {
        __intercomInitialized?: boolean;
      dataLayer: any[]
    }
}