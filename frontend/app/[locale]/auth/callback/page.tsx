"use client";

import { useRouter } from "@/i18n/navigation";
import { setToken } from "@/lib/auth";
import { useSearchParams } from "next/navigation";
import { Suspense, useEffect } from "react";

function CallbackInner() {
  const params = useSearchParams();
  const router = useRouter();

  useEffect(() => {
    const token = params.get("token");
    if (token) {
      setToken(token);
      router.replace("/student/dashboard");
    } else {
      router.replace("/login");
    }
  }, [params, router]);

  return (
    <section className="section">
      <p className="text-center text-win-muted">Signing you in...</p>
    </section>
  );
}

export default function AuthCallbackPage() {
  return (
    <Suspense fallback={<section className="section">Loading...</section>}>
      <CallbackInner />
    </Suspense>
  );
}
