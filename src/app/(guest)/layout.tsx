import React from "react";
import MegatraderScreen from "@/components/MegatraderScreen";

const Layout = ({ children }: { children: React.ReactNode }) => {
  return (
    <div className="h-dvh lg:flex lg:flex-1">
      <div className="flex h-full md:h-auto w-full lg:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none xl:px-24">
        <div className="mx-auto w-full md:max-w-[638px] lg:max-w-[409px]">
          <div className="w-full mx-auto space-y-8">{children}</div>
        </div>
      </div>
      <div className="relative hidden rounded-2xl lg:w-0 lg:rounded-2xl m-4 pb-8 lg:flex-1 md:flex bg-[#1E1E1E] lg:overflow-hidden sm:flex sm:justify-center items-center">
        <MegatraderScreen />
      </div>
    </div>
  );
};

export default Layout;
