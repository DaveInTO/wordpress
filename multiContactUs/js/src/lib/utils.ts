import type { ClassValue } from "clsx"
import { clsx } from "clsx"
import { twMerge } from "tailwind-merge"

const PREFIX = "yc:"

export function cn(...inputs: ClassValue[]) {
  const merged = twMerge(clsx(inputs))

  return merged
    .split(/\s+/)
    .map(cls => {
      // skip empties or already prefixed
      if (!cls || cls.startsWith(PREFIX)) return cls

      // handle variants like hover:, sm:, dark:, etc.
      const parts = cls.split(":")
      const base = parts.pop()!
      return [...parts, `${PREFIX}${base}`].join(":")
    })
    .join(" ")
}
