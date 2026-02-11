import argparse
import asyncio
import edge_tts


async def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--text", required=True)
    parser.add_argument("--out", required=True)
    parser.add_argument("--voice", default="vi-VN-HoaiMyNeural")
    parser.add_argument("--rate", default="+0%", help="Speaking rate, e.g. -20%% for slower, +20%% for faster")
    args = parser.parse_args()

    communicate = edge_tts.Communicate(args.text, args.voice, rate=args.rate)
    await communicate.save(args.out)
    print(f"Audio saved to {args.out}")


if __name__ == "__main__":
    asyncio.run(main())
