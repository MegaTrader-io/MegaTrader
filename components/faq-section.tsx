"use client";

import { useState } from "react";
import Link from "next/link";
import { Plus, Minus } from "lucide-react";
import { faqs } from "@/lib/data";
import { cn } from "@/lib/utils";

export function FAQSection() {
  const [openIndex, setOpenIndex] = useState(0);

  return (
    <section id="faqs" className="py-12 lg:py-20">
      <div className="mx-auto max-w-7xl px-4 lg:px-8">
        {/* Header */}
        <header className="text-center">
          <h2 className="text-3xl font-light uppercase text-white lg:text-4xl">
            FREQUENTLY ASKED{" "}
            <span className="text-primary-400">QUESTIONS</span>
          </h2>
        </header>

        {/* FAQ Accordion */}
        <div className="mx-auto mt-12 max-w-3xl">
          <div className="flex flex-col gap-4">
            {faqs.map((faq, index) => (
              <div
                key={index}
                className={cn(
                  "overflow-hidden rounded-2xl border transition-colors",
                  openIndex === index
                    ? "border-primary-400 bg-gray-800"
                    : "border-gray-700 bg-gray-800/50"
                )}
              >
                <button
                  onClick={() => setOpenIndex(openIndex === index ? -1 : index)}
                  className="flex w-full items-center justify-between p-6 text-left"
                >
                  <span className="pr-4 text-lg font-medium text-white">
                    {faq.question}
                  </span>
                  <div className="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-700">
                    {openIndex === index ? (
                      <Minus className="h-5 w-5 text-white" />
                    ) : (
                      <Plus className="h-5 w-5 text-white" />
                    )}
                  </div>
                </button>
                <div
                  className={cn(
                    "overflow-hidden transition-all duration-300",
                    openIndex === index
                      ? "max-h-96 opacity-100"
                      : "max-h-0 opacity-0"
                  )}
                >
                  <div className="px-6 pb-6 text-gray-400">{faq.answer}</div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Support Card */}
        <div className="mx-auto mt-12 max-w-3xl">
          <div className="rounded-2xl border border-gray-700 bg-gray-800 p-8">
            <div className="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <div className="text-sm font-medium text-gray-400">
                  Have more questions?
                </div>
                <div className="mt-1 text-xl font-medium text-white">
                  Get lightning fast support
                </div>
                <p className="mt-2 text-gray-400">
                  Find instant answers in our Help Center, or ask us on Discord.
                </p>
              </div>
              <div className="flex flex-col gap-3 sm:flex-row">
                <Link
                  href="https://help.megatrader.io/en/"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="rounded-lg bg-secondary-500 px-6 py-3 text-center text-sm font-medium uppercase text-gray-950 transition-colors hover:bg-secondary-600"
                >
                  OPEN HELP CENTER
                </Link>
                <Link
                  href="https://discord.com/invite/megatrader"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="rounded-lg border border-gray-600 px-6 py-3 text-center text-sm font-medium uppercase text-white transition-colors hover:bg-gray-700"
                >
                  CHECK DISCORD
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
