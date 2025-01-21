import clsx from 'clsx';
import {ElementType} from "react";

interface PaginationProps extends React.HTMLAttributes<HTMLElement> {
    'aria-label'?: string;
}

export function Pagination({
                               'aria-label': ariaLabel = 'Page navigation',
                               className,
                               ...props
                           }: PaginationProps) {
    return <nav aria-label={ariaLabel} {...props} className={clsx(className, 'flex gap-x-2')}/>;
}

interface PaginationPreviousProps extends React.HTMLAttributes<HTMLSpanElement> {
    href?: string | null;
    children?: React.ReactNode;
}

export function PaginationPrevious({
                                       href = null,
                                       className,
                                       children = 'Previous',
                                   }: PaginationPreviousProps) {
    return (
        <span className={clsx(className, 'grow basis-0')}>
      <button {...(href === null ? {disabled: true} : {href})} aria-label="Previous page">
        <svg className="stroke-current" data-slot="icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <path
              d="M2.75 8H13.25M2.75 8L5.25 5.5M2.75 8L5.25 10.5"
              strokeWidth={1.5}
              strokeLinecap="round"
              strokeLinejoin="round"
          />
        </svg>
          {children}
      </button>
    </span>
    );
}

interface PaginationNextProps extends React.HTMLAttributes<HTMLSpanElement> {
    href?: string | null;
    children?: React.ReactNode;
}

export function PaginationNext({
                                   href = null,
                                   className,
                                   children = 'Next',
                               }: PaginationNextProps) {
    return (
        <span className={clsx(className, 'flex grow basis-0 justify-end')}>
      <button {...(href === null ? {disabled: true} : {href})} aria-label="Next page">
        {children}
          <svg className="stroke-current" data-slot="icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <path
              d="M13.25 8L2.75 8M13.25 8L10.75 10.5M13.25 8L10.75 5.5"
              strokeWidth={1.5}
              strokeLinecap="round"
              strokeLinejoin="round"
          />
        </svg>
      </button>
    </span>
    );
}

type PaginationListProps = React.HTMLAttributes<HTMLSpanElement>

export function PaginationList({className, ...props}: PaginationListProps) {
    return <div {...props} className={clsx(className, 'flex gap-2')}/>;
}

interface PaginationPageProps extends React.HTMLAttributes<HTMLSpanElement> {
    href?: string;
    current?: boolean;
    children: React.ReactNode;
    as?: ElementType;
}

export function PaginationPage({
                                   className,
                                   current = false,
                                   children,
                                   as: Component = 'a',
                                    ...props
                               }: PaginationPageProps) {
    return (
        <Component
            aria-label={`Page ${children}`}
            aria-current={current ? 'page' : undefined}
            className={clsx(
                className,
                '',
                current && 'before:bg-zinc-950/5 dark:before:bg-white/10'
            )}
            {...props}
        >
            {children}
        </Component>
    );
}

interface PaginationGapProps extends React.HTMLAttributes<HTMLSpanElement> {
    children?: React.ReactNode;
}

export function PaginationGap({
                                  className,
                                  children = <>&hellip;</>,
                                  ...props
                              }: PaginationGapProps) {
    return (
        <span
            aria-hidden="true"
            {...props}
            className={clsx(
                className,
                'w-[2.25rem] select-none text-center text-sm/6 font-semibold text-zinc-950 dark:text-white'
            )}
        >
      {children}
    </span>
    );
}
