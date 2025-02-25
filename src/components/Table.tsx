'use client'

import clsx from 'clsx';
import {createContext, useContext, useState, ReactNode, HTMLAttributes,} from 'react';
import Link from "next/link";

interface TableContextProps {
    bleed?: boolean;
    dense?: boolean;
    grid?: boolean;
    striped?: boolean;
    className?: string;
    children?: ReactNode;
}

const TableContext = createContext<TableContextProps>({
    bleed: false,
    dense: false,
    grid: false,
    striped: false,
});

interface TableProps extends HTMLAttributes<HTMLDivElement>, TableContextProps {
    className?: string;
    children: ReactNode;
}

export function Table({
                          bleed = false,
                          dense = false,
                          grid = false,
                          striped = false,
                          className,
                          children,
                          ...props
                      }: TableProps) {
    return (
        <TableContext.Provider value={{bleed, dense, grid, striped}}>
            <div className="flow-root">
                <div {...props} className={clsx(className, '-mx-[--gutter] overflow-x-auto whitespace-nowrap')}>
                    <div className={clsx('inline-block min-w-full align-middle', !bleed && 'sm:px-[--gutter]')}>
                        <table
                            className="min-w-full text-left text-sm/6 text-zinc-950 dark:text-white">{children}</table>
                    </div>
                </div>
            </div>
        </TableContext.Provider>
    );
}

interface TableHeadProps extends HTMLAttributes<HTMLTableSectionElement> {
    className?: string;
}

export function TableHead({className, ...props}: TableHeadProps) {
    return <thead {...props} className={clsx(className, 'text-zinc-500 dark:text-zinc-400')}/>;
}

export function TableBody(props: HTMLAttributes<HTMLTableSectionElement>) {
    return <tbody {...props} />;
}

interface TableRowContextProps {
    href?: string;
    target?: string;
    title?: string;
}

const TableRowContext = createContext<TableRowContextProps>({
    href: undefined,
    target: undefined,
    title: undefined,
});

interface TableRowProps extends HTMLAttributes<HTMLTableRowElement>, TableRowContextProps {
    className?: string;
}

export function TableRow({href, target, title, className, ...props}: TableRowProps) {
    const {striped} = useContext(TableContext);

    return (
        <TableRowContext.Provider value={{href, target, title}}>
            <tr
                {...props}
                className={clsx(
                    className,
                    href &&
                    'has-[[data-row-link][data-focus]]:outline has-[[data-row-link][data-focus]]:outline-2 has-[[data-row-link][data-focus]]:-outline-offset-2 has-[[data-row-link][data-focus]]:outline-blue-500 dark:focus-within:bg-white/[2.5%]',
                    striped && 'even:bg-zinc-950/[2.5%] dark:even:bg-white/[2.5%]',
                    href && striped && 'hover:bg-zinc-950/5 dark:hover:bg-white/5',
                    href && !striped && 'hover:bg-zinc-950/[2.5%] dark:hover:bg-white/[2.5%]'
                )}
            />
        </TableRowContext.Provider>
    );
}

interface TableHeaderProps extends HTMLAttributes<HTMLTableCellElement> {
    className?: string;
}

export function TableHeader({className, ...props}: TableHeaderProps) {
    const {bleed, grid} = useContext(TableContext);

    return (
        <th
            {...props}
            className={clsx(
                className,
                'border-b border-b-zinc-950/10 text-xs px-4 py-2 font-bold leading-tight first:pl-[var(--gutter,theme(spacing.2))] last:pr-[var(--gutter,theme(spacing.2))] dark:border-b-white/10',
                grid && 'border-l border-l-zinc-950/5 first:border-l-0 dark:border-l-white/5',
                !bleed && 'sm:first:pl-1 sm:last:pr-1'
            )}
        />
    );
}

interface TableCellProps extends HTMLAttributes<HTMLTableCellElement> {
    className?: string;
    colSpan?: number;
    children?: ReactNode;
}

export function TableCell({className, children, ...props}: TableCellProps) {
    const {bleed, dense, grid, striped} = useContext(TableContext);
    const {href, target, title} = useContext(TableRowContext);
    const [cellRef, setCellRef] = useState<HTMLTableCellElement | null>(null);

    return (
        <td
            ref={href ? setCellRef : undefined}
            {...props}
            className={clsx(
                className,
                'relative px-4 first:pl-[var(--gutter,theme(spacing.2))] last:pr-[var(--gutter,theme(spacing.2))]',
                !striped && 'border-b border-zinc-950/5 dark:border-white/5',
                grid && 'border-l border-l-zinc-950/5 first:border-l-0 dark:border-l-white/5',
                dense ? 'py-2.5' : 'py-4',
                !bleed && 'sm:first:pl-1 sm:last:pr-1'
            )}
        >
            {href && (
                <Link
                    data-row-link
                    href={href}
                    target={target}
                    aria-label={title}
                    tabIndex={cellRef?.previousElementSibling === null ? 0 : -1}
                    className="absolute inset-0 focus:outline-none"
                />
            )}
            {children}
        </td>
    );
}
