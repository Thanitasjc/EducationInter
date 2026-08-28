"use client";

import { createContext, type ReactNode, useContext, useEffect, useState } from "react";
import { initializeLiff } from "@/lib/liff";

type LiffState = {
  initialized: boolean;
  isInClient: boolean;
  error: string | null;
};

const initialState: LiffState = {
  initialized: false,
  isInClient: false,
  error: null,
};

const LiffContext = createContext<LiffState>(initialState);

export function LiffProvider({ children }: { children: ReactNode }) {
  const [state, setState] = useState<LiffState>(initialState);

  useEffect(() => {
    initializeLiff()
      .then(({ initialized, isInClient }) => {
        setState({ initialized, isInClient, error: null });

        if (process.env.NODE_ENV === "development") {
          console.debug("LIFF initialized", { initialized, isInClient });
        }
      })
      .catch((error: unknown) => {
        const message = error instanceof Error ? error.message : "Unable to initialize LIFF.";
        setState({ initialized: false, isInClient: false, error: message });

        if (process.env.NODE_ENV === "development") {
          console.debug("LIFF initialization failed", error);
        }
      });
  }, []);

  return <LiffContext.Provider value={state}>{children}</LiffContext.Provider>;
}

export function useLiff(): LiffState {
  return useContext(LiffContext);
}

export function LiffDebugStatus() {
  const state = useLiff();

  if (process.env.NODE_ENV !== "development") {
    return null;
  }

  return (
    <p className="text-center text-xs text-win-muted" data-testid="liff-debug-status">
      LIFF: {state.initialized ? "initialized" : "initializing"} | in client: {String(state.isInClient)}
      {state.error ? ` | error: ${state.error}` : ""}
    </p>
  );
}
