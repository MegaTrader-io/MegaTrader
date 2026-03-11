"use client";

import { useEffect, useState } from "react";
import { Award } from "lucide-react";
import { verifiedCertificates } from "@/lib/data";

export function VerifiedSection() {
  // Duplicate for infinite scroll effect
  const certificates = [...verifiedCertificates, ...verifiedCertificates, ...verifiedCertificates];

  return (
    <section className="overflow-hidden py-12 lg:py-20">
      <div className="mx-auto max-w-7xl px-4 lg:px-8">
        {/* Header */}
        <header className="text-center">
          <h2 className="text-3xl font-light uppercase text-white lg:text-4xl">
            Verified <span className="text-primary-400">ACHIEVEMENTS</span> from
            Real Traders
          </h2>
          <p className="mx-auto mt-4 max-w-3xl text-lg font-light text-gray-400 lg:text-xl">
            Every certificate represents a trader moving forward — from passing
            evaluations to receiving payouts. This is just the beginning of
            what&apos;s possible.
          </p>
        </header>
      </div>

      {/* Scrolling Carousel */}
      <div className="mt-12 overflow-hidden">
        <div className="animate-scroll flex gap-4">
          {certificates.map((cert, index) => (
            <CertificateCard key={index} title={cert.title} index={index} />
          ))}
        </div>
      </div>

      <style jsx>{`
        @keyframes scroll {
          0% {
            transform: translateX(0);
          }
          100% {
            transform: translateX(-33.333%);
          }
        }
        .animate-scroll {
          animation: scroll 30s linear infinite;
        }
        .animate-scroll:hover {
          animation-play-state: paused;
        }
      `}</style>
    </section>
  );
}

function CertificateCard({ title, index }: { title: string; index: number }) {
  const isWithdrawal = title.includes("Withdrawal");

  return (
    <div className="group flex h-48 w-72 flex-shrink-0 cursor-pointer flex-col items-center justify-center gap-4 rounded-2xl border-4 border-gray-800 bg-gradient-to-br from-gray-800 to-gray-900 p-6 transition-colors hover:border-primary-400">
      <Award
        className={`h-16 w-16 ${
          isWithdrawal ? "text-secondary-400" : "text-primary-400"
        }`}
      />
      <div className="text-center">
        <div className="font-medium text-white">{title}</div>
        <div className="mt-1 text-sm text-gray-400">
          {isWithdrawal ? "Withdrawal Verified" : "Challenge Passed"}
        </div>
      </div>
    </div>
  );
}
