import type { ReactNode } from "react";
import { LiffAuthProvider } from "@/components/LiffAuthProvider";

export function AppProviders({ children }: { children: ReactNode }) {
  return <LiffAuthProvider>{children}</LiffAuthProvider>;
}
