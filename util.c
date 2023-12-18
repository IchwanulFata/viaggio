#include <string.h>
#include <ctype.h>
#include <stdlib.h>
#include <sys/time.h>
#include <windows.h>
#include "index-tools.h"
#include "util.h"
#include "define.h"

/* struct for timing */
static LARGE_INTEGER start_time; /* the starting time */
static LARGE_INTEGER stop_time;  /* the ending time */
static LARGE_INTEGER frequency;  /* ticks per second */

int startTiming(void)
{
    QueryPerformanceFrequency(&frequency);
    QueryPerformanceCounter(&start_time);
    return 1;
}

int stopTiming(void)
{
    QueryPerformanceCounter(&stop_time);
    return 1;
}

float timingDuration(void)
{
    return ((float)(stop_time.QuadPart - start_time.QuadPart) / frequency.QuadPart);
}


void getword(FILE *fd, char *word)
{
  int chr;
  char *pw=word;

  /* ignored if chr is a white space */
  while(isspace(chr=fgetc(fd)));
  if(isalpha(chr))
  {
    *pw++=chr;
  }
  while(chr!=EOF)
  {
	if(!isalnum(*pw=fgetc(fd)))
	  break;
	pw++;
  }
  *pw='\0';
}


int binarySearch(StopList *stoplist, int num, char *word)
{
  int high=num-1, low=0, mid;
  int val;

  while(low<=high)
  {
    mid=(low+high)/2;
    val=strcmp(stoplist[mid].word,word);
    if(val<0)
    {
      low=mid+1;
    }
    else if(val>0)
    {
      high=mid-1;
    }
    else
    {
      return (mid);
    }
  }
  return ((val==0) ? mid : -1);
}

/* check whether the word is a stopword */
int isstopword(char *word, int totlist, StopList *stoplist)
{
  /* check wheather the word found in the array
     of stoplist */
  if(binarySearch(stoplist,totlist,word)<0)
  {
    return 0;  /* not found */
  }
  return 1;    /* found */
}


/* convert word to lower case */
void wordToLower(char *word)
{
  char *pw=word;
  for(;*pw!='\0';pw++)
  {
    if(isupper(*pw))
    {
      *pw=tolower(*pw);
    }
  }
}

/* load stoplist to array */
int loadStopList(StopList stoplist[])
{
  int i;
  FILE * fstop;
  char buffer[WORDLEN];

  if((fstop=fopen("stoplist","r"))==NULL)
  {
    printf("Opening file stoplist failed...\n");
    return 0;
  }
  else
  {
    for(i=0;fscanf(fstop,"%s",buffer)==1;i++)
	{
	  stoplist[i].word=malloc(strlen(buffer)+1);
	  strcpy(stoplist[i].word,buffer);
    }
  }
  return 1;
}


