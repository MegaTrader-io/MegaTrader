import Link from "next/link";
import { payoutSteps } from "@/lib/data";

export function HowItWorks() {
  return (
    <section id="how-it-works" className="py-12 lg:py-20">
      <div className="mx-auto max-w-7xl px-4 lg:px-8">
        <div className="flex flex-col gap-12 lg:flex-row lg:items-center lg:gap-16">
          {/* Content */}
          <div className="flex flex-1 flex-col gap-8">
            <h2 className="text-3xl font-light uppercase leading-tight text-white lg:text-4xl">
              Earn your first payout in <span className="text-primary-400">7 days</span>
            </h2>

            {/* Steps */}
            <ol className="flex flex-col gap-8">
              {payoutSteps.map((step, index) => (
                <li
                  key={index}
                  className="border-l-2 border-secondary-400 bg-gradient-to-r from-secondary-500/10 to-transparent py-2 pl-4"
                >
                  <h3 className="text-xl font-bold text-secondary-400">
                    {index + 1}. {step.title}
                  </h3>
                  <p className="mt-2 text-base font-medium tracking-tight text-gray-400">
                    {step.description}
                  </p>
                </li>
              ))}
            </ol>

            {/* CTA */}
            <div>
              <Link
                href="#pricing"
                className="inline-flex rounded-lg bg-primary-400 px-6 py-3 text-base font-medium uppercase text-gray-950 transition-colors hover:bg-primary-500"
              >
                Get Funded Now
              </Link>
            </div>
          </div>

          {/* Video */}
          <div className="flex-1">
            <div className="aspect-video overflow-hidden rounded-2xl bg-gray-800">
              <iframe
                src="https://player.vimeo.com/video/1153328572?badge=0&autopause=0&player_id=0&app_id=58479"
                className="h-full w-full"
                allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media"
                title="Welcome MegaTrader"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
