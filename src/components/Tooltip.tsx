import * as React from "react";
import {
    useFloating,
    autoUpdate,
    offset,
    flip,
    shift,
    useHover,
    useFocus,
    useDismiss,
    useRole,
    useInteractions,
    useMergeRefs,
    FloatingPortal,
    arrow
} from "@floating-ui/react";
import type {Placement} from "@floating-ui/react";
import clsx from "clsx";

interface TooltipOptions {
    initialOpen?: boolean;
    placement?: Placement;
    open?: boolean;
    onOpenChange?: (open: boolean) => void;
}

export function useTooltip({
                               initialOpen = false,
                               placement = "top",
                               open: controlledOpen,
                               onOpenChange: setControlledOpen
                           }: TooltipOptions = {}) {
    const [uncontrolledOpen, setUncontrolledOpen] = React.useState(initialOpen);

    const open = controlledOpen ?? uncontrolledOpen;
    const setOpen = setControlledOpen ?? setUncontrolledOpen;

    const arrowRef = React.useRef<HTMLDivElement | null>(null);

    const data = useFloating({
        placement,
        open,
        onOpenChange: setOpen,
        whileElementsMounted: autoUpdate,
        middleware: [
            offset(12),
            flip({
                crossAxis: placement.includes("-"),
                fallbackAxisSideDirection: "start",
                padding: 5
            }),
            shift({padding: 5}),
            arrow({element: arrowRef})
        ]
    });

    const context = data.context;

    const hover = useHover(context, {
        move: false,
        enabled: controlledOpen == null
    });
    const focus = useFocus(context, {
        enabled: controlledOpen == null
    });
    const dismiss = useDismiss(context);
    const role = useRole(context, {role: "tooltip"});

    const interactions = useInteractions([hover, focus, dismiss, role]);

    return React.useMemo(
        () => ({
            open,
            setOpen,
            arrowRef,
            ...interactions,
            ...data
        }),
        [open, setOpen, interactions, data]
    );
}

type ContextType = ReturnType<typeof useTooltip> | null;

const TooltipContext = React.createContext<ContextType>(null);

export const useTooltipContext = () => {
    const context = React.useContext(TooltipContext);

    if (context == null) {
        throw new Error("Tooltip components must be wrapped in <Tooltip />");
    }

    return context;
};

export function Tooltip({
                            children,
                            ...options
                        }: { children: React.ReactNode } & TooltipOptions) {
    const tooltip = useTooltip(options);
    return (
        <TooltipContext.Provider value={tooltip}>{children}</TooltipContext.Provider>
    );
}

export const TooltipTrigger = React.forwardRef<
    HTMLElement,
    React.HTMLProps<HTMLElement> & { asChild?: boolean }
>(function TooltipTrigger({children, asChild = false, ...props}, propRef) {
    const context = useTooltipContext();
    const childrenRef = (children as any).ref;
    const ref = useMergeRefs([context.refs.setReference, propRef, childrenRef]);

    if (asChild && React.isValidElement(children)) {
        const mergedProps = {
            ref,
            ...props,
            ...(typeof children.props === "object" ? children.props : {}),
            "data-state": context.open ? "open" : "closed",
        } as React.HTMLProps<HTMLElement>;

        return React.cloneElement(children, mergedProps);
    }

    return (
        <button
            ref={ref}
            data-state={context.open ? "open" : "closed"}
            {...context.getReferenceProps(props)}
        >
            {children}
        </button>
    );
});

export const TooltipContent = React.forwardRef<
    HTMLDivElement,
    React.HTMLProps<HTMLDivElement>
>(function TooltipContent({style, className, ...props}, propRef) {
    const context = useTooltipContext();
    const ref = useMergeRefs([context.refs.setFloating, propRef]);

    if (!context.open) return null;

    return (
        <FloatingPortal>
            <div
                ref={ref}
                style={{
                    ...context.floatingStyles,
                    ...style
                }}
                {...context.getFloatingProps(props)}
                className={clsx('px-3 py-2 bg-black rounded-lg border border-neutral-700 box-border w-max max-w-[calc(100vw-10px)] text-stone-400', className)}
            >
                {props.children}
                <div
                    ref={context.arrowRef}
                    className="absolute -bottom-2"
                    style={{
                        left: context.middlewareData.arrow?.x ? context.middlewareData.arrow?.x + 1 : '',
                        bottom: context.middlewareData.arrow?.y ?? ''
                    }}
                >
                    <svg width="16" height="10" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g filter="url(#filter0_d_4674_1364)">
                            <path d="M8 8L16 0H0L8 8Z" fill="black"/>
                        </g>
                        <defs>
                            <filter id="filter0_d_4674_1364" x="0" y="0" width="16" height="10"
                                    filterUnits="userSpaceOnUse" colorInterpolationFilters="sRGB">
                                <feFlood floodOpacity="0" result="BackgroundImageFix"/>
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                               values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="2"/>
                                <feComposite in2="hardAlpha" operator="out"/>
                                <feColorMatrix type="matrix"
                                               values="0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 1 0"/>
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_4674_1364"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_4674_1364"
                                         result="shape"/>
                            </filter>
                        </defs>
                    </svg>
                </div>
            </div>
        </FloatingPortal>
    );
});
